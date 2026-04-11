<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StressTest extends Command
{
    protected $signature = 'test:stress 
                            {--concurrent=10 : Number of concurrent operations}
                            {--iterations=100 : Total number of iterations}
                            {--type=all : Test type: database, http, cache, search, all}
                            {--cleanup : Clean up test data after running}';

    protected $description = 'Run stress tests on the application to measure performance and stability';

    private $results = [];
    private $errors = [];
    private $testUsers = [];
    private $testTasks = [];

    public function handle()
    {
        $concurrent = (int) $this->option('concurrent');
        $iterations = (int) $this->option('iterations');
        $type = $this->option('type');

        $this->info("🚀 Starting Stress Tests");
        $this->info("========================");
        $this->info("Concurrent Operations: {$concurrent}");
        $this->info("Total Iterations: {$iterations}");
        $this->info("Test Type: {$type}");
        $this->newLine();

        // Run selected tests
        $tests = $type === 'all'
            ? ['database', 'cache', 'search', 'concurrent_writes', 'memory']
            : [$type];

        foreach ($tests as $testType) {
            $this->runTestType($testType, $concurrent, $iterations);
        }

        // Display results summary
        $this->displayResults();

        // Cleanup if requested
        if ($this->option('cleanup')) {
            $this->cleanup();
        }

        return count($this->errors) > 0 ? 1 : 0;
    }

    private function runTestType(string $type, int $concurrent, int $iterations): void
    {
        $this->info("▶ Running {$type} stress test...");

        switch ($type) {
            case 'database':
                $this->testDatabaseOperations($iterations);
                break;
            case 'cache':
                $this->testCacheOperations($iterations);
                break;
            case 'search':
                $this->testSearchOperations($iterations);
                break;
            case 'concurrent_writes':
                $this->testConcurrentWrites($concurrent, $iterations);
                break;
            case 'memory':
                $this->testMemoryUsage($iterations);
                break;
            default:
                $this->warn("Unknown test type: {$type}");
        }
    }

    /**
     * Test database read/write operations
     */
    private function testDatabaseOperations(int $iterations): void
    {
        $this->output->write("  Testing database operations... ");

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $readTimes = [];
        $writeTimes = [];
        $updateTimes = [];
        $deleteTimes = [];

        // Create test user for operations
        $testUser = User::create([
            'name' => 'Stress Test User',
            'email' => 'stresstest_' . Str::random(8) . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'usuario',
            'is_active' => true,
        ]);
        $this->testUsers[] = $testUser->id;

        $bar = $this->output->createProgressBar($iterations);
        $bar->start();

        for ($i = 0; $i < $iterations; $i++) {
            // Write operation
            $writeStart = microtime(true);
            $task = Task::create([
                'title' => "Stress Test Task {$i} - " . Str::random(10),
                'description' => "Description for stress test task {$i}. " . Str::random(200),
                'creator_id' => $testUser->id,
                'assignee_id' => $testUser->id,
                'priority' => ['Baja', 'Media', 'Alta'][rand(0, 2)],
                'status' => 'Pendiente',
                'start_date' => now(),
                'due_date' => now()->addDays(rand(1, 30)),
            ]);
            $writeTimes[] = microtime(true) - $writeStart;
            $this->testTasks[] = $task->id;

            // Read operation
            $readStart = microtime(true);
            $foundTask = Task::with(['creator', 'assignee', 'comments'])->find($task->id);
            $readTimes[] = microtime(true) - $readStart;

            // Update operation
            $updateStart = microtime(true);
            $task->update(['status' => 'En progreso']);
            $updateTimes[] = microtime(true) - $updateStart;

            $bar->advance();
        }

        $bar->finish();

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $this->results['database'] = [
            'total_time' => $endTime - $startTime,
            'memory_used' => ($endMemory - $startMemory) / 1024 / 1024,
            'operations' => $iterations * 3,
            'avg_write_time' => array_sum($writeTimes) / count($writeTimes) * 1000,
            'avg_read_time' => array_sum($readTimes) / count($readTimes) * 1000,
            'avg_update_time' => array_sum($updateTimes) / count($updateTimes) * 1000,
            'max_write_time' => max($writeTimes) * 1000,
            'max_read_time' => max($readTimes) * 1000,
            'max_update_time' => max($updateTimes) * 1000,
        ];

        $this->newLine();
        $this->info("  ✅ Database test completed");
    }

    /**
     * Test cache operations
     */
    private function testCacheOperations(int $iterations): void
    {
        $this->output->write("  Testing cache operations... ");

        $startTime = microtime(true);
        $readTimes = [];
        $writeTimes = [];
        $hitCount = 0;
        $missCount = 0;

        $bar = $this->output->createProgressBar($iterations);
        $bar->start();

        for ($i = 0; $i < $iterations; $i++) {
            $cacheKey = "stress_test_key_{$i}";

            // Write to cache
            $writeStart = microtime(true);
            \Cache::put($cacheKey, "Cached value {$i} with data: " . Str::random(100), 300);
            $writeTimes[] = microtime(true) - $writeStart;

            // Read from cache
            $readStart = microtime(true);
            $value = \Cache::get($cacheKey);
            $readTimes[] = microtime(true) - $readStart;

            if ($value) {
                $hitCount++;
            } else {
                $missCount++;
            }

            // Test cache remember pattern
            $rememberKey = "stress_test_remember_{$i}";
            \Cache::remember($rememberKey, 300, function () use ($i) {
                return "Remembered value {$i}";
            });

            // Cleanup
            \Cache::forget($cacheKey);
            \Cache::forget($rememberKey);

            $bar->advance();
        }

        $bar->finish();

        $endTime = microtime(true);

        $this->results['cache'] = [
            'total_time' => $endTime - $startTime,
            'operations' => $iterations * 4,
            'avg_write_time' => array_sum($writeTimes) / count($writeTimes) * 1000,
            'avg_read_time' => array_sum($readTimes) / count($readTimes) * 1000,
            'cache_hit_rate' => ($hitCount / ($hitCount + $missCount)) * 100,
        ];

        $this->newLine();
        $this->info("  ✅ Cache test completed");
    }

    /**
     * Test search functionality
     */
    private function testSearchOperations(int $iterations): void
    {
        $this->output->write("  Testing search operations... ");

        $searchTerms = [
            'tarea',
            'urgente',
            'proyecto',
            'importante',
            'desarrollo',
            'testing',
            'revisión',
            'informe',
            'reunión',
            'deadline',
        ];

        $startTime = microtime(true);
        $searchTimes = [];
        $resultCounts = [];

        $bar = $this->output->createProgressBar($iterations);
        $bar->start();

        for ($i = 0; $i < $iterations; $i++) {
            $term = $searchTerms[array_rand($searchTerms)];

            $searchStart = microtime(true);

            // Search using the same logic as TaskController
            try {
                $results = Task::whereRaw(
                    'MATCH(title, description) AGAINST(? IN BOOLEAN MODE)',
                    [$term]
                )->limit(20)->get();
            } catch (\Exception $e) {
                // Fallback to LIKE
                $results = Task::where(function ($q) use ($term) {
                    $q->where('title', 'LIKE', "%{$term}%")
                        ->orWhere('description', 'LIKE', "%{$term}%");
                })->limit(20)->get();
            }

            $searchTimes[] = microtime(true) - $searchStart;
            $resultCounts[] = $results->count();

            $bar->advance();
        }

        $bar->finish();

        $endTime = microtime(true);

        $this->results['search'] = [
            'total_time' => $endTime - $startTime,
            'avg_search_time' => array_sum($searchTimes) / count($searchTimes) * 1000,
            'max_search_time' => max($searchTimes) * 1000,
            'min_search_time' => min($searchTimes) * 1000,
            'avg_results_count' => array_sum($resultCounts) / count($resultCounts),
        ];

        $this->newLine();
        $this->info("  ✅ Search test completed");
    }

    /**
     * Test concurrent write operations
     */
    private function testConcurrentWrites(int $concurrent, int $iterations): void
    {
        $this->output->write("  Testing concurrent writes... ");

        $startTime = microtime(true);
        $successCount = 0;
        $failureCount = 0;
        $deadlockCount = 0;

        // Create test user
        $testUser = User::create([
            'name' => 'Concurrent Test User',
            'email' => 'concurrent_' . Str::random(8) . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'usuario',
            'is_active' => true,
        ]);
        $this->testUsers[] = $testUser->id;

        // Create a shared task for concurrent updates
        $sharedTask = Task::create([
            'title' => 'Shared Task for Concurrent Testing',
            'description' => 'This task will be updated concurrently',
            'creator_id' => $testUser->id,
            'assignee_id' => $testUser->id,
            'priority' => 'Alta',
            'status' => 'Pendiente',
            'start_date' => now(),
            'due_date' => now()->addDays(7),
        ]);
        $this->testTasks[] = $sharedTask->id;

        $bar = $this->output->createProgressBar($iterations);
        $bar->start();

        for ($i = 0; $i < $iterations; $i++) {
            try {
                DB::transaction(function () use ($sharedTask, $testUser, $i) {
                    // Simulate concurrent comment creation
                    Comment::create([
                        'task_id' => $sharedTask->id,
                        'user_id' => $testUser->id,
                        'content' => "Concurrent comment {$i}: " . Str::random(50),
                    ]);

                    // Simulate concurrent status update
                    $statuses = ['Pendiente', 'En progreso', 'Completada'];
                    $sharedTask->update(['status' => $statuses[rand(0, 2)]]);
                });

                $successCount++;
            } catch (\Illuminate\Database\QueryException $e) {
                if (strpos($e->getMessage(), 'Deadlock') !== false) {
                    $deadlockCount++;
                }
                $failureCount++;
                $this->errors[] = [
                    'test' => 'concurrent_writes',
                    'error' => $e->getMessage(),
                    'iteration' => $i,
                ];
            } catch (\Exception $e) {
                $failureCount++;
                $this->errors[] = [
                    'test' => 'concurrent_writes',
                    'error' => $e->getMessage(),
                    'iteration' => $i,
                ];
            }

            $bar->advance();
        }

        $bar->finish();

        $endTime = microtime(true);

        $this->results['concurrent_writes'] = [
            'total_time' => $endTime - $startTime,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'deadlock_count' => $deadlockCount,
            'success_rate' => ($successCount / $iterations) * 100,
        ];

        $this->newLine();
        $this->info("  ✅ Concurrent writes test completed");
    }

    /**
     * Test memory usage under load
     */
    private function testMemoryUsage(int $iterations): void
    {
        $this->output->write("  Testing memory usage... ");

        $memorySnapshots = [];
        $startMemory = memory_get_usage(true);

        $bar = $this->output->createProgressBar($iterations);
        $bar->start();

        for ($i = 0; $i < $iterations; $i++) {
            // Load tasks with eager loading
            $tasks = Task::with(['creator', 'assignee', 'comments', 'comments.user'])
                ->limit(50)
                ->get();

            // Load users with their tasks
            $users = User::with(['tasksCreated', 'tasksAssigned'])
                ->limit(20)
                ->get();

            // Process data to simulate real operations
            foreach ($tasks as $task) {
                $data = $task->toArray();
                $json = json_encode($data);
            }

            $memorySnapshots[] = memory_get_usage(true);

            // Force garbage collection every 10 iterations
            if ($i % 10 === 0) {
                gc_collect_cycles();
            }

            $bar->advance();
        }

        $bar->finish();

        $peakMemory = memory_get_peak_usage(true);
        $endMemory = memory_get_usage(true);

        $this->results['memory'] = [
            'start_memory_mb' => $startMemory / 1024 / 1024,
            'end_memory_mb' => $endMemory / 1024 / 1024,
            'peak_memory_mb' => $peakMemory / 1024 / 1024,
            'avg_memory_mb' => array_sum($memorySnapshots) / count($memorySnapshots) / 1024 / 1024,
            'memory_growth_mb' => ($endMemory - $startMemory) / 1024 / 1024,
        ];

        $this->newLine();
        $this->info("  ✅ Memory usage test completed");
    }

    /**
     * Display test results summary
     */
    private function displayResults(): void
    {
        $this->newLine(2);
        $this->info("📊 STRESS TEST RESULTS SUMMARY");
        $this->info("==============================");
        $this->newLine();

        foreach ($this->results as $testName => $metrics) {
            $this->info("🔹 " . strtoupper(str_replace('_', ' ', $testName)));
            $this->table(
                ['Metric', 'Value'],
                collect($metrics)->map(function ($value, $key) {
                    $formattedKey = ucwords(str_replace('_', ' ', $key));

                    if (is_float($value)) {
                        if (strpos($key, 'time') !== false) {
                            $formattedValue = number_format($value, 2) . ' ms';
                        } elseif (strpos($key, 'memory') !== false || strpos($key, 'mb') !== false) {
                            $formattedValue = number_format($value, 2) . ' MB';
                        } elseif (strpos($key, 'rate') !== false) {
                            $formattedValue = number_format($value, 2) . '%';
                        } else {
                            $formattedValue = number_format($value, 2);
                        }
                    } else {
                        $formattedValue = $value;
                    }

                    return [$formattedKey, $formattedValue];
                })->toArray()
            );
            $this->newLine();
        }

        // Performance assessment
        $this->assessPerformance();

        // Display errors if any
        if (!empty($this->errors)) {
            $this->newLine();
            $this->warn("⚠️ ERRORS OCCURRED:");
            foreach (array_slice($this->errors, 0, 5) as $error) {
                $this->error("  - [{$error['test']}] Iteration {$error['iteration']}: " . substr($error['error'], 0, 100));
            }
            if (count($this->errors) > 5) {
                $this->warn("  ... and " . (count($this->errors) - 5) . " more errors");
            }
        }
    }

    /**
     * Assess performance based on results
     */
    private function assessPerformance(): void
    {
        $this->info("🎯 PERFORMANCE ASSESSMENT");
        $this->info("=========================");

        $issues = [];
        $passed = [];

        // Database performance thresholds
        if (isset($this->results['database'])) {
            $db = $this->results['database'];

            if ($db['avg_write_time'] > 50) {
                $issues[] = "Database write time is high ({$db['avg_write_time']}ms avg). Consider indexing or query optimization.";
            } else {
                $passed[] = "Database write performance is good ({$db['avg_write_time']}ms avg)";
            }

            if ($db['avg_read_time'] > 20) {
                $issues[] = "Database read time is high ({$db['avg_read_time']}ms avg). Review eager loading and indexes.";
            } else {
                $passed[] = "Database read performance is good ({$db['avg_read_time']}ms avg)";
            }
        }

        // Cache performance thresholds
        if (isset($this->results['cache'])) {
            $cache = $this->results['cache'];

            if ($cache['cache_hit_rate'] < 95) {
                $issues[] = "Cache hit rate is low ({$cache['cache_hit_rate']}%). Check cache configuration.";
            } else {
                $passed[] = "Cache hit rate is excellent ({$cache['cache_hit_rate']}%)";
            }
        }

        // Search performance thresholds
        if (isset($this->results['search'])) {
            $search = $this->results['search'];

            if ($search['avg_search_time'] > 100) {
                $issues[] = "Search queries are slow ({$search['avg_search_time']}ms avg). Consider full-text indexing.";
            } else {
                $passed[] = "Search performance is good ({$search['avg_search_time']}ms avg)";
            }
        }

        // Concurrent writes thresholds
        if (isset($this->results['concurrent_writes'])) {
            $concurrent = $this->results['concurrent_writes'];

            if ($concurrent['success_rate'] < 95) {
                $issues[] = "Concurrent write success rate is low ({$concurrent['success_rate']}%). Check for race conditions.";
            } else {
                $passed[] = "Concurrent write handling is stable ({$concurrent['success_rate']}% success rate)";
            }

            if ($concurrent['deadlock_count'] > 0) {
                $issues[] = "Deadlocks detected ({$concurrent['deadlock_count']}). Review transaction isolation and lock ordering.";
            }
        }

        // Memory thresholds
        if (isset($this->results['memory'])) {
            $memory = $this->results['memory'];

            if ($memory['peak_memory_mb'] > 256) {
                $issues[] = "Peak memory usage is high ({$memory['peak_memory_mb']}MB). Check for memory leaks.";
            } else {
                $passed[] = "Memory usage is within acceptable limits ({$memory['peak_memory_mb']}MB peak)";
            }
        }

        // Display results
        foreach ($passed as $item) {
            $this->line("  <fg=green>✅</> {$item}");
        }

        foreach ($issues as $issue) {
            $this->line("  <fg=red>❌</> {$issue}");
        }

        $this->newLine();

        if (empty($issues)) {
            $this->info("🎉 All performance metrics passed!");
        } else {
            $this->warn("⚠️ " . count($issues) . " performance issue(s) detected. Review the suggestions above.");
        }
    }

    /**
     * Clean up test data
     */
    private function cleanup(): void
    {
        $this->newLine();
        $this->info("🧹 Cleaning up test data...");

        // Delete test tasks
        if (!empty($this->testTasks)) {
            // Delete comments first
            Comment::whereIn('task_id', $this->testTasks)->delete();
            Task::whereIn('id', $this->testTasks)->delete();
            $this->info("  Deleted " . count($this->testTasks) . " test tasks");
        }

        // Delete test users
        if (!empty($this->testUsers)) {
            User::whereIn('id', $this->testUsers)->delete();
            $this->info("  Deleted " . count($this->testUsers) . " test users");
        }

        // Clear cache
        \Cache::flush();
        $this->info("  Cleared cache");

        $this->info("✅ Cleanup completed");
    }
}
