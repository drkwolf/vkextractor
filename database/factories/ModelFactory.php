<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\Models\Data::class, function (Faker\Generator $faker) {
  static $password;
  $friends = fakeFriends($faker);

  return [
    'friends' => json_encode($friends),
    'messages' => json_encode(fakeUserDialogs($friends, $faker)),
  ];
});


function fakeFriends(Faker\Generator $faker) {
  $count = $faker->randomNumber(1);
  $items = [];
  for($i=0; $i < $count; $i++) {
    $items[$i] = $faker->unique()->numberBetween($min=1, $max=$count);
  }
  return compact('count', 'items');
}


/**
 * generate dialogue for random set of Friends
 * @param $friends
 * @param \Faker\Generator $faker
 * @return array
 */
function fakeUserDialogs($friends, Faker\Generator $faker) {
  $count = $faker->numberBetween($min=1, $max = array_get($friends, 'count'));
  $items = [];
  $f_items = array_get($friends, 'items');

  for($i=0; $i < $count; $i++) {
    $id = $faker->randomElement($f_items, 1);
    $f_items = array_diff( $f_items, [$id] );
    $items[$i] = fakeUserMessages($id, $faker);
  }
  return compact('count', 'items');
}

/**
 * generates message for spefic user
 * @param friend_id $
 * @param \Faker\Generator $faker
 * @return array
 */
function fakeUserMessages($friend_id, Faker\Generator $faker) {
  $count = $faker->numberBetween($min=10, $max=1000);
  $items = [];
  $dates = [];

  for($i=0; $i< $count; $i++) {
    $dates[] = $faker->unique()->unixTime();
  }
  asort($dates);
  for($i=0; $i< $count; $i++) {
    $items[$i] = [
      'date' => $dates[$i],
      'form_id' => $friend_id,
//      'user_id' => 0, // not needed
//      'body' => 0, // not needed
    ];
  }
  return compact('count', 'items');
}
