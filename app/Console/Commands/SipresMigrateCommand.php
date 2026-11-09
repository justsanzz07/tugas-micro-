<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;

class SipresMigrateCommand extends Command
{
    protected $signature = 'sipres:migrate {--fresh : Drop all tables and re-run all migrations} {--seed : Seed the databases after migrating}';

    protected $description = 'Run migrations across all SIPRES database connections (submission, workflow, finance)';

    /**
     * @var array<string, array{path: string, db_env: string, default_db: string}>
     */
    protected array $databases = [
        'sipres_submission' => [
            'path' => 'database/migrations/sipres_submission',
            'db_env' => 'DB_SUBMISSION_DATABASE',
            'default_db' => 'sipres_submission',
        ],
        'sipres_workflow' => [
            'path' => 'database/migrations/sipres_workflow',
            'db_env' => 'DB_WORKFLOW_DATABASE',
            'default_db' => 'sipres_workflow',
        ],
        'sipres_finance' => [
            'path' => 'database/migrations/sipres_finance',
            'db_env' => 'DB_FINANCE_DATABASE',
            'default_db' => 'sipres_finance',
        ],
    ];

    public function handle(): int
    {
        $this->info('🚀 Memulai migrasi database SIPRES...');

        // 1. Ensure databases exist on MySQL if host is accessible
        $this->ensureDatabasesExist();

        // 2. Default app tables (users, sessions, jobs, cache)
        $this->info('📦 Migrating core application tables (connection: default)...');
        $this->call($this->option('fresh') ? 'migrate:fresh' : 'migrate', [
            '--force' => true,
        ]);

        // 3. Migrate each SIPRES module database
        foreach ($this->databases as $connection => $info) {
            $this->newLine();
            $this->info("🔄 Migrating [{$connection}] -> {$info['path']}...");

            $params = [
                '--database' => $connection,
                '--path' => $info['path'],
                '--force' => true,
            ];

            if ($this->option('fresh')) {
                $this->call('migrate:fresh', $params);
            } else {
                $this->call('migrate', $params);
            }
        }

        if ($this->option('seed')) {
            $this->newLine();
            $this->info('🌱 Seeding database...');
            $this->call('db:seed', ['--force' => true]);
        }

        $this->newLine();
        $this->info('✅ Seluruh database SIPRES berhasil dimigrasi!');

        return Command::SUCCESS;
    }

    protected function ensureDatabasesExist(): void
    {
        try {
            $host = config('database.connections.mysql.host', '127.0.0.1');
            $port = config('database.connections.mysql.port', 3306);
            $user = config('database.connections.mysql.username', 'root');
            $pass = config('database.connections.mysql.password', '');

            $pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            foreach ($this->databases as $info) {
                $dbName = env($info['db_env'], $info['default_db']);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                $this->line("  ✓ Database `{$dbName}` siap.");
            }
        } catch (\Throwable $e) {
            $this->warn('  ⚠️ Tidak dapat membuat database otomatis via PDO (MySQL mungkin belum aktif atau kredensial berbeda).');
        }
    }
}
