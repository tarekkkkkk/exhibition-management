<?php

namespace Database\Factories;
use APP\Models\Post;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */

//  $factory -> define(Post::class,fuction (Faker $faker){
// return[
    

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'name' => $faker->name,
            // 'Descriptions' => $faker->text
        ];
    }
}
