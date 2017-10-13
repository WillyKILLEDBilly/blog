<?php

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\User;
use App\models\Like;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('UserSeeder');

        $faker = Faker\Factory::create();
        $users = User::where('activated', true)->get();

        //posts
        foreach ($users as $user)
        {
        	$count = rand(10,15);
        	for($i=0; $i<$count; $i++)
	        {
	        	$post = new Post;
	        	$post->text = $faker->paragraphs($nb = 6, $asText = true);
	        	$post->header = $faker->sentence($nbWords = 6, $variableNbWords = true);
	        	$post->user_id=$user->id;
	        	$post->save();
	        }
        }

        $posts = Post::all();

        //likes
        foreach ($posts as $post)
        {
            foreach($users as $user)
            {
                if (rand(0,1)) {
                    $like = new Like;
                    $like->user_id = $user->id;
                    $like->post_id = $post->id;
                    $like->state = true;
                    $like->save();
                }
            }
        }
    }
}
