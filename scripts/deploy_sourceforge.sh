#!/bin/bash -e
parentdir="$(dirname "$(pwd)")/BFAdminCP"

if [[ -v CIRCLE_BRANCH ]]; then
    FILENAME="${CIRCLE_BRANCH}.zip"
elif [[ -v CIRCLE_TAG ]]; then
    FILENAME="${CIRCLE_TAG}.zip"
else
    exit 1
fi

cat > .env << EOF
APP_KEY=VIskgutkOS1aLQBOiIxxYYyJpaTiTMxb

DB_HOST=127.0.0.1
DB_DATABASE=circle_test
DB_USERNAME=ubuntu
DB_PASSWORD=
APP_ENV=testing
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_DRIVER=sync
MAIL_DRIVER=log

# Add your IPv4 or IPv6 address here to show more detailed debug
# information and also view the system maintence page.
# You can add more than one ip address here if you need too.
# Just seprate each address with the pipe character (|).
#
# Example:
#
# 127.0.0.1|10.0.0.1|172.0.0.1
#
# By default it allows only the localhost
IP_WHITELIST=127.0.0.1

# Set your pusher API ID, Key, and Secret here.
# You will need an account at https://pusher.com
PUSHER_APP_ID=null
PUSHER_KEY=null
PUSHER_SECRET=null
EOF

/home/ubuntu/.phpenv/shims/php artisan optimize

rm -f .env

echo "Zipping application into ${FILENAME}."
zip -9 -q -r $FILENAME . -x .git*
USER="prophet731"
HOST="frs.sourceforge.net"
echo "Uploading ${FILENAME} to sourceforge."
scp -oUserKnownHostsFile=/dev/null "${parentdir}/${FILENAME}" $USER@$HOST:/home/pfs/project/b/bf/bfacp/
echo "${FILENAME} uploaded."
