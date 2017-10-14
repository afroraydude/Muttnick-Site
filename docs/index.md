# Welcome
## Installation

### With Docker
1. Clone the git repoisitory
2. In the root directory of the repository, run `docker-compose up`
3. Access the site from `http://localhost:8080` and go through the site setup there

### Without docker

1. Install NGINX, PHP7-FPM, and MySQL
2. Clone the git repo within your root www directory
3. Replace the default NGINX site config `default` with the file `default.nginx` found within the repo
3. Restart NGINX
4. Create a MySQL user and database for this site
5. Visit the site at `http://localhost` and follow the site setup there

## Credits and documentation
All software used through composer belong to their respective owners.

For documentation on what this project uses most directly visit these links:

[Slim Framework](https://slimframework.com) - The backbone for this project's code

[HTML to Markdown](https://github.com/thephpleague/html-to-markdown) - Used to 
make it easier for creating and editing pages/posts

[CommonMark](https://github.com/thephpleague/commonmark) - Turns these edited pages
into HTML
