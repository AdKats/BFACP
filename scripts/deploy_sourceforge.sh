#!/bin/bash -eu
parentdir="$(dirname "$(pwd)")/BFAdminCP"
FILENAME="${CIRCLE_BRANCH:?CIRCLE_TAG}.zip"
zip -9 -q -r $FILENAME . -x *.git*
USER="prophet731"
HOST="frs.sourceforge.net"
scp -oUserKnownHostsFile=/dev/null "${parentdir}/${FILENAME}" $USER@$HOST:/home/pfs/project/b/bf/bfacp/
