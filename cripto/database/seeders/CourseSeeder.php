<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run()
    {
        $faker = Faker::create();

        // 1. Course Categories
        $categories = [];
        for ($i = 1; $i <= 8; $i++) {
            $title = $faker->words(2, true);
            $categories[] = [
                'title'       => ucfirst($title),
                'slug'        => Str::slug($title) . '-' . $i,
                'description' => $faker->sentence(),
                'image_path'  => 'uploads/categories/category' . $i . '.jpg',
                'status'      => $faker->boolean(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }
        DB::table('course_categories')->insert($categories);

        // 2. Courses
        $courses = [];
        for ($i = 1; $i <= 8; $i++) {
            $name = $faker->sentence(3);
            $courses[] = [
                'category_id'    => $faker->numberBetween(1, 8),
                'name'           => ucfirst($name),
                'slug'           => Str::slug($name) . '-' . $i,
                'author'         => $faker->name(),
                'price'          => $faker->randomFloat(2, 100, 1000),
                'description'    => $faker->paragraph(),
                'featured'       => $faker->boolean(),
                'status'         => $faker->boolean(),
                'payment_status' => $faker->randomElement(['unpaid', 'paid']),
                'feature_image'  => 'uploads/courses/course' . $i . '.jpg',
                'rating'         => $faker->randomFloat(2, 1, 5),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }
        DB::table('courses')->insert($courses);

        // 3. Course Sessions
        $sessions = [];
        for ($i = 1; $i <= 30; $i++) {
            $title = 'Session ' . $i;
            $sessions[] = [
                'course_id'  => $faker->numberBetween(1, 8),
                'title'      => $title,
                'description'=> $faker->sentence(),
                'file_path'  => 'uploads/sessions/session' . $i . '.mp4',
                'mime_type'  => 'video/mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('course_sessions')->insert($sessions);
    }
}
