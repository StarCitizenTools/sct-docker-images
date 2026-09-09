<div align="center">

# Star Citizen Wiki Docker

[Docker Hub](https://hub.docker.com/r/starcitizentools/mediawiki) | [Kubernetes config](https://github.com/StarCitizenTools/sct-k8-config)

</div>

The Docker configuration powering https://starcitizen.tools

## Images

| Image | Directory | Docker Hub Tag |
|-------|-----------|----------------|
| MediaWiki | `mediawiki/` | `starcitizentools/mediawiki:smw-latest` |
| Jobrunner | `jobrunner/` | `starcitizentools/mediawiki:smw-jobrunner-latest` |
| Nginx | `nginx/` | `starcitizentools/nginx:latest` |

## Dependency updates

Every external input is pinned to an exact version where Dependabot can see
it, and `.github/dependabot.yml` opens weekly grouped PRs to bump them:

- Base images (`php`, `nginx`, `composer`, `php-extension-installer`) are
  `tag@sha256:digest` on `FROM` lines, so the build cache only changes when a
  commit changes it, even if a tag were ever re-pushed. Images only
  used via `COPY --from` are declared as named stages for that reason.
- The jobrunner (`jobrunner/mediawiki-services-jobrunner`) is a git submodule
  tracking `weirdgloop/master`, so the image ships an exact commit and a PR
  appears when Weird Gloop pushes. Clone with `--recurse-submodules`.
- MediaWiki core deliberately tracks the head of `MEDIAWIKI_BRANCH` at build
  time (`mediawiki/Dockerfile`); set `MEDIAWIKI_COMMIT_HASH` to freeze it.

## Releasing

A push to `main` builds and publishes all three images under one calver stamp,
then dispatches `app-images-built` to sct-k8-config, whose `bump-app-images`
workflow opens a "Bump Docker images to <stamp>" PR. Merging that PR deploys.
The dispatch needs the `K8_CONFIG_DISPATCH_TOKEN` secret (fine-grained PAT,
Contents read/write on sct-k8-config); without it the build still succeeds and
the bump is done by hand from sct-k8-config's Actions tab.

## Building

All images are built together using [Docker Bake](https://docs.docker.com/build/bake/):

```bash
docker buildx bake
```

To build a single image:

```bash
docker buildx bake mediawiki
docker buildx bake jobrunner
docker buildx bake nginx
```
