While working on the test task, I implemented two approaches, they are both available on separate branches:

branch main - an asynchronous solution using CQRS style and Symfony Messenger
branch alternative/synchronous-version - a traditional synchronous solution to demonstrate an alternative approach to solve

In both cases "domain logic" stays the same which means that it is independent from the environment and the approach of implementation.

Anyway this is quite a trivial task and choosing the right solution highly depends on the environment (project context understanding), circumstances and team experience.
