
# RMS Test Task — Synchronous Version

This is a Symfony-based Dockerized application designed to process and analyze an aggregated log file that continuously receives input from multiple services. This version follows a **traditional synchronous approach**, without background workers or messaging systems.

## Requirements

- Docker
- GNU Make

## Installation

To install and set up the application, run:

```bash
make install
```

This command performs the following steps:

1. Builds Docker images.
2. Copies and prepares PHP configuration files.
3. Launches the MariaDB database container and waits until it's ready.
4. Prepares the environment files.
5. Installs PHP dependencies via Composer.
6. Runs database migrations for both default and test environments.
7. Starts application containers.

> Note: The `make install` execution will also create `.env` and `compose.override.yml` files.

## Testing

To run the quality assurance tools (e.g., linters, static analysis):

```bash
make test
```

## Usage

To parse a log file, run:

```bash
make run
```

This executes the log parsing command:

```bash
docker exec -it -e XDEBUG_MODE=off $(COMPOSE_PROJECT_NAME)_php bin/console app:parse-log ./data/logs.log 10 -vv
```

- `./data/logs.log` — path to the input log file.
- `10` — batch size.

You can modify both parameters in the `make run` rule or run the command manually.

> No separate terminal or worker setup is required — the entire processing is synchronous and completes in a single run.

## Additional Commands

- **Access PHP shell:**
  ```bash
  make sh
  ```

- **Clean up containers and untracked files:**
  ```bash
  make clean
  ```

- **View raw SQL logs:**
  ```bash
  make db-log
  ```

## API Documentation

Once the containers are running, the OpenAPI documentation is available via Swagger UI at:

```
http://127.0.0.1:8080/api/doc
```

## Implementation Notes

This application uses a generator-based parser to efficiently read and process large log files without consuming excessive memory. To reduce load on the database and improve performance, log entries are inserted in batches. Indexes are created on key fields — such as service name, status code, and timestamp — to ensure fast query responses, especially for filtered aggregations like those used in the `/count` endpoint.

In a real-world environment with a continuously growing log file potentially reaching terabytes in size, this architecture would eventually face scalability limitations. For such high-throughput analytical workloads, a column-oriented database (e.g., ClickHouse or Apache Druid) would be a more suitable choice due to their efficiency in handling large volumes of data and aggregation-heavy queries. However, for the purpose of this test task, the current solution is intentionally kept simple and sufficient for demonstration.
