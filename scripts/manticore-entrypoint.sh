#!/bin/sh
set -eu

# Enable command tracing if desired
[ -n "${ENTRYPOINT_TRACE:-}" ] && set -x

# If we're running RT then invoke the initialisation hooks first
if [ "$1" = "searchd" ]; then
  # Run the scripts in our entrypoint hooks directory
  /bin/run-parts --report --exit-on-error /entrypoint.d
fi

exec "$@"

# vim: ai ts=2 sw=2 et sts=2 ft=sh