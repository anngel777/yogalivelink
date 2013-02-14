# Use PHP5 as default - 1and1.com
AddType x-mapp-php5 .php 

<Files .htaccess>
order allow,deny
deny from all
</Files>

DirectoryIndex index
Options +FollowSymlinks

RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !^(.+)/wo/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.+)/admin+/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.+)/libupdate\.php$
RewriteCond %{REQUEST_FILENAME} !^(.+)/wo_update\.php$
RewriteCond %{REQUEST_FILENAME} !^(.+)/class_update\.php$
RewriteCond %{REQUEST_FILENAME} !^(.*)/common/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.*)/content/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.*)/templates/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.*)/helper/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.*)/css/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.*)/js/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.*)/jslib/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.*)/images/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.*)/admin\+/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.*)/lib/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.*)/install/(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.*)/packages/(.*)$
RewriteCond %{REQUEST_FILENAME} !^/index(.*)$
RewriteCond %{REQUEST_FILENAME} !^(.+)\.ico$
RewriteCond %{REQUEST_FILENAME} !^(.+)\.pdf$
RewriteCond %{REQUEST_FILENAME} !^(.+)\.swf$
RewriteCond %{REQUEST_FILENAME} !^(.+)\.asc$
RewriteCond %{REQUEST_FILENAME} !^(.+)/robots\.txt$
RewriteCond %{REQUEST_FILENAME} !^(.+)/archive\.php$
RewriteCond %{REQUEST_FILENAME} !^(.+)/page\.php$
RewriteRule ^(.*)\.* /page.php?$1

IndexIgnore *