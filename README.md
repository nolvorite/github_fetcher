## Prerequisites
- PHPMyAdmin/a mySQL server
- A blank database
- Permission to edit files in the root directory of this folder

## Already Supplied
- REDIS Details (.example.env)

## Installation Instructions

In Git...

- Run "git clone git@github.com:nolvorite/github_fetcher.git"
- Run  "composer install"
- Run "php artisan key:generate"
- Run your database configuration is correct in your .env file
- Run "php artisan migrate"
- Run "npm install"
- Run "npm run dev"

Once everything is up and running, run "php artisan serve".

- Register an account

## Running the API endpoint

- Make sure you're logged in.
- You can either fetch the data in the site (to see the data yourself), or 
- Simply go http://127.0.0.1:8000/fetch_user_data?usernames={usernames} where {usernames} is a list of usernames, with each of them separated by a comma.

## Live Example

- You can go to https://github-fetcher.notecanvas.com for a live example of this in action.