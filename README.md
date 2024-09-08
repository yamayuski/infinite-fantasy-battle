# Infinite FANTASY BATTLE simulator

## Install

Add `ifb.test` and `api.ifb.test` to hosts file

Install mkcert

```ps1
# in host computer
mkcert ifb.test "*.ifb.test"
```

copy pem file to `/docker/ingress/cert.pem` and `/docker/ingress/key.pem`.

```sh
docker compose up -d
```

## License

See [LICENSE](LICENSE).

## Code of conduct

See [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md).

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## Security

See [SECURITY.md](SECURITY.md).
