
# RMS test task

This is a Symfony-based Dockerized application designed to process and analyze a large aggregated log file that continuously receives input from multiple services.

##  Requirements

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
8. Launches the Symfony Messenger worker (in foreground).

> Note: The `make install` execution will also create `.env` and `compose.override.yml` files.

## Testing

To run the quality assurance tools (e.g., linters, static analysis):

```bash
make test
```

## Usage

After running `make install`, the Symfony Messenger worker will be started automatically and kept active in the first terminal. This worker logs processing activity and should remain open. For running additional commands (such as log parsing), open a **second terminal**.

### Terminal 1: Worker (started by `make install`)

The `make install` command automatically launches the async worker:

```bash
make install
```

The worker runs in the foreground and displays live processing output. Keep this terminal open.

### Terminal 2: Using the application

Open a second terminal for any interactive use, including parsing logs:

```bash
make run
```

This executes the log parsing command:

```bash
docker exec -it -e XDEBUG_MODE=off $(COMPOSE_PROJECT_NAME)_php bin/console app:parse-log ./data/logs.log 10
```

- `./data/logs.log` – path to the input log file.
- `10` – batch size.

You can modify these parameters in the `make run` rule or run the command manually with your own values.

## Additional Commands

- **Access PHP shell:**
  ```bash
  make sh
  ```

- **Stop async worker manually:**
  ```bash
  make stop-workers
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

## Implementation notes
This application uses a generator-based parser to efficiently read and process large log files without consuming excessive memory. To reduce load on the database and improve performance, log entries are inserted in batches. Indexes are created on key fields — such as service name, status code, and timestamp — to ensure fast query responses, especially for filtered aggregations like those used in the /count endpoint.

In a real-world environment with a continuously growing log file potentially reaching terabytes in size, this architecture would eventually face scalability limitations. For such high-throughput analytical workloads, a column-oriented database (e.g., ClickHouse or Apache Druid) would be a more suitable choice due to their efficiency in handling large volumes of data and aggregation-heavy queries. However, for the purpose of this test task, the current solution is intentionally kept simple and sufficient for demonstration.
