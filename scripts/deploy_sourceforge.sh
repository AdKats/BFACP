#!/bin/bash -eu
parentdir="$(dirname "$(pwd)")"
#FILENAME=${CIRCLE_BRANCH}
USER="prophet731"
HOST="frs.sourceforge.net"
scp -oUserKnownHostsFile=/dev/null "${parentdir}/README.md" $USER@$HOST:/home/pfs/project/b/bf/bfacp/
