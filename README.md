# Github Dashboard

This little toy project aims to provide some features Github's currently missing, like:

* Powerful orgs/projects feeds supporting query, custom feeds, ...
* More insights: insights on 14 days are pretty much useless

## Architecture

Github Dashboard uses Clean Architecture. Each specific domain has it's own namespace (like `Ingestion`).

3 layers of tests are used:

1. SpecBDD, written with phpspec, for encompassing good design
2. Integration tests, written with phpunit, mostly for DB interactions
3. End-to-end tests, written with behat, to ensure key API endpoints are working as expected.

### Ingestion

First, Github sends an event to the hook. This raw event is stored as-is in a first database (of your choise), before 
it's passed to a transformation subsystem. Finally, the transformed event is stored in Prometheus, a time series DB. 
