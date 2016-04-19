#!/bin/bash -e
parentdir="$(dirname "$(pwd)")/BFAdminCP"

if [[ -v CIRCLE_BRANCH ]]; then
    FILENAME="${CIRCLE_BRANCH}.zip"
elif [[ -v CIRCLE_TAG ]]; then
    FILENAME="${CIRCLE_TAG}.zip"
else
    exit 1
fi
echo "Zipping application into ${FILENAME}."
zip -9 -q -r $FILENAME . -x *.git*
USER="prophet731"
HOST="frs.sourceforge.net"
echo "Uploading ${FILENAME} to source forge"
scp -oUserKnownHostsFile=/dev/null "${parentdir}/${FILENAME}" $USER@$HOST:/home$
echo "${FILENAME} uploaded."
