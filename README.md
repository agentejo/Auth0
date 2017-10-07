# Configuration

In the Cockpit yaml `config/config.yaml`:

```
auth0:
    enable: true
    domain: company.eu.auth0.com
    secret: xxxxLG1Phms1LsZAnrNe3xxxxxx
    database: connection-database-name
```

In Auth0 user_metadata

```
{
    "cockpit": {
        "group": "admin"
    }
}
```

### 💐 SPONSORED BY

[![ginetta](https://user-images.githubusercontent.com/321047/29219315-f1594924-7eb7-11e7-9d58-4dcf3f0ad6d6.png)](https://www.ginetta.net)<br>
We create websites and apps that click with users.
