<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\User;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create data user .sql namun tetap melewati model agar ter protect oleh aturan database nya
        // Read the SQL file
        $path = database_path('sql/datauser-lite.sql');
        
        if (File::exists($path)) {
            $sql = File::get($path);
            // Parse and insert data
            $this->insertDataFromSql($sql);
            $this->command->info('Data Mahasiswa berhasil disemai dari datauser-lite.sql!');
        } else {
            $this->command->error('File datauser-lite.sql tidak ditemukan!');
        }
    }

    protected function insertDataFromSql($sql)
    {
        // Parse the SQL file into individual insert statements
        $statements = explode(';', $sql);

        foreach ($statements as $statement) {
            // Skip empty statements
            if (trim($statement) == '') {
                continue;
            }

            // Extract data from the insert statement
            if (preg_match('/INSERT INTO users \(([^)]+)\) VALUES \(([^)]+)\)/i', $statement, $matches)) {
                $columns = explode(',', str_replace('`', '', $matches[1]));
                $values = explode(',', $matches[2]);

                // Trim and remove quotes from columns and values
                $columns = array_map('trim', $columns);
                $values = array_map(function ($value) {
                    return trim($value, " '");
                }, $values);

                // Combine columns and values into an associative array
                $data = array_combine($columns, $values);

                // Create user using the model to trigger events
                User::create($data);
            } else {
                $this->command->warn('No match: ' . $statement);
            }
        }
    }
}
