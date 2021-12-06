#!/bin/bash

# This is required so we can change the command that is run when starting up
# i.e. `docker run php-fpm "php ./some-script.php"`
if [ "$#" -gt "0" ]; then
	$@
	exit "$?"
fi


echo "#!/bin/bash"                      > /wrap-env
printenv | grep -v "^CRON_" | sed 's/^\([A-Z_]*\)=\(.*\)$/export \1="\2"/g' >> /wrap-env
echo '"$@"'                              >> /wrap-env

chmod +x /wrap-env

cronfile=/tmp/cron
cat /dev/null > $cronfile

echo "SHELL=/bin/bash" >> $cronfile
echo "PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin" >> $cronfile

for cronvar in ${!CRON_*}; do
	cronvalue=${!cronvar}
	echo "Installing $cronvar"
	echo "$cronvalue  > /proc/1/fd/1 2>/proc/1/fd/2" >> $cronfile
done
echo >> $cronfile # Newline is required

cat /dev/null > /etc/crontab
cat /tmp/cron | crontab

cron -f