FROM nginx:1.21

LABEL maintainer="Adam Kirk <adam_kirk@live.co.uk>"

RUN apt-get update 
RUN apt-get install -y vim less curl

# We will add our own
RUN rm /etc/nginx/nginx.conf /etc/nginx/conf.d/default.conf

COPY nginx.conf /etc/nginx/nginx.conf
COPY mime.types /etc/nginx/mime.types
COPY conf.d/default.conf.template /etc/nginx/conf.d/default.conf.template

# There is an include directive within default.conf.template in the server directive
# It includes these files allowing extra configuration to be supplied from higher up the stack
RUN mkdir /etc/nginx/conf.d/extras

# Files in here will be copied into /etc/nginx/conf.d/extras by the entrypoint
# This allows us to keep this image dumb, yet configurable
RUN mkdir /docker-entrypoint-extras.d

# We've got a custom entrypoint script, which isn't doing much right now
# In the future it should contain a test/wait loop for the php-fpm instance
# it should only start when it can reach php-fpm
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Set the default port for applications built using this image
EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
