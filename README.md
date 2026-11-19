<div align="center">

# Star Citizen Wiki Docker

[Kubernetes config](https://github.com/StarCitizenTools/sct-k8-config)

</div>

The Docker configuration powering https://starcitizen.tools

## Images

| Image | Directory | Tag |
|-------|-----------|----------------|
| MediaWiki | `mediawiki/` | `ghcr.io/starcitizentools/mediawiki:smw` |
| Jobrunner | `jobrunner/` | `ghcr.io/starcitizentools/mediawiki:smw-jobrunner` |
| Nginx | `nginx/` | `ghcr.io/starcitizentools/nginx` |

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

## Local patches

Our changes to MediaWiki core and extensions live in `mediawiki/patches/`,
one change per `.patch` file. Some are fixes waiting on an upstream release;
others are local modifications that upstream doesn't want or that only make
sense for this wiki. Each file starts with a short header:

- what it changes and why;
- `Upstream:` the task or change it tracks, or why it stays local;
- `Drop:` when it can go, if it is meant to be temporary.

A unified diff follows, with paths relative to the MediaWiki root
(`a/includes/...`, `a/extensions/<Name>/...`). To make one, put the original
file under `a/` and your edited copy under `b/`, then run
`diff -u a/<path> b/<path>` and add the header.

The build applies them in name order after Composer, with
`patch --fuzz=0 --forward`, and lints every file they touch. A patch that no
longer applies exactly, because the code around it changed or upstream now
has the same change, fails the build; update or delete it then.

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
