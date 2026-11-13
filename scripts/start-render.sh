#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

php artisan migrate --force

php artisan queue:work \
    --queue="${QUEUE_NAME:-default}" \
    --sleep="${QUEUE_SLEEP:-3}" \
    --tries="${QUEUE_TRIES:-1}" \
    --timeout="${QUEUE_TIMEOUT:-600}" \
    > storage/logs/queue-worker.log 2>&1 &
worker_pid=$!

php artisan serve --host=0.0.0.0 --port="${PORT:-10000}" &
web_pid=$!

cleanup() {
    kill "$worker_pid" "$web_pid" 2>/dev/null || true
}
trap cleanup EXIT INT TERM

while kill -0 "$web_pid" 2>/dev/null; do
    if ! kill -0 "$worker_pid" 2>/dev/null; then
        echo "Queue worker exited unexpectedly; stopping the web process." >&2
        exit 1
    fi
    sleep 2
done

wait "$web_pid"
