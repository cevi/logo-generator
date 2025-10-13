# Cevi-Logo Generator

## Use it

[Logo-Generator](https://logo.cevi.ch)

## Preset content

[logo.cevi.ch?logo-left=CEVI&logo-right=WÄDI-AU](https://logo.cevi.ch?logo-left=CEVI&logo-right=WÄDI-AU&claim-left=FREUNDE&claim-right=%20FÜRS%20LEBEN)

Possible GET-Parameter:
- `logo-left`
- `logo-right`
- `logo-right-second`
- `claim-left`
- `claim-right`
- `color=[web|print|black]`

## Get directly the logo

You can get your logo directly from our website:

[https://logo.cevi.ch/logo.svg?left=LOGO&right=GENERATOR](https://logo.cevi.ch/logo.svg?left=LOGO&right=GENERATOR)

![https://logo.cevi.ch/logo.svg?left=LOGO&right=GENERATOR](https://logo.cevi.ch/logo.svg?left=LOGO&right=GENERATOR)

## Use the generator on your website

You can add the logo-generator to your website via an iframe:

```html
<iframe src="https://logo.cevi.ch/?iframe=true"
        width="100%"
        height="700"
        title=""></iframe>
```


## Installation

This project is based on `npm`. Please [install](https://www.npmjs.com/get-npm) it on your system.
To build the frontend (CSS & Javascript), please install the following modules:

```shell script
$ npm install -g browserify
```

Install now the node-packages via `npm`:

```shell script
$ npm install
```

The access to the admin-page is restricted. Copy the file `./admin/example.env` to `./admin/.env` and add there your own hashed password:

```php
echo password_hash("your password here", PASSWORD_BCRYPT, $options);
```

## Development

Now you can use the following commands to build your local website:

```shell script
$ npm run build
```


## Examples

- [cevi.ws/cevi-online/logo-generator](https://www.cevi.ws/cevi-online/logo-generator)


## Contributors

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<table>
  <tr>
    <td align="center">
        <a href="https://github.com/btemperli">
            <img src="https://avatars.githubusercontent.com/u/3005632?v=4?s=100" width="100px;" alt=""/>
            <br />
            <sub><b>Beat Temperli</b></sub>
        </a>
        <br />
        <a href="https://github.com/cevi/logo-generator/commits?author=btemperli" title="Code">💻</a> <a href="https://github.com/cevi/logo-generator/commits?author=btemperli" title="Documentation">📖</a> <a href="https://github.com/cevi/logo-generator/issues?q=author%3Abtemperli" title="Ideas, Planning, & Feedback">🤔</a> <a href="https://github.com/cevi/logo-generator/issues?q=author%3Abtemperli" title="Maintenance">🚧</a>
    </td>
    <td align="center">
        <a href="https://github.com/wp99cp">
            <img src="https://avatars.githubusercontent.com/u/34008738?v=4?s=100" width="100px;" alt=""/>
            <br />
            <sub><b>Cyrill Püntener</b></sub>
        </a>
        <br />
        <a href="https://github.com/cevi/logo-generator/issues?q=author%3Awp99cp" title="Ideas, Planning, & Feedback">🤔</a> <a href="https://github.com/cevi/logo-generator/issues?q=author%3Awp99cp" title="Reported a bug">🐛</a>
    </td>
  </tr>
</table>

