# Muttnick
A simple, lightweight, and fast way to create websites.
## Installation

### With Docker
1. Clone the git repoisitory
2. In the root directory of the repository, run `docker-compose up`
3. Access the site from `http://localhost:8080` and go through the site setup there

### Without docker

1. Install NGINX, PHP7-FPM, and MySQL
2. Clone the git repo within your root www directory
3. Replace the default NGINX site config `default` with the file `default` found within the repo
3. Restart NGINX
4. Create a MySQL user and database for this site
5. Visit the site at `http://localhost` and follow the site setup there