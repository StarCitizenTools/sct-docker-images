<?php
/**
 * Versioned file URLs — cache-busting for reuploads.
 *
 * Appends a short content-version token (first 5 characters of the file's
 * SHA-1) to original and thumbnail URLs: .../Foo.png?abc12. A reupload
 * changes the token and therefore the URL, so browsers and Cloudflare fetch
 * the new file immediately instead of serving a stale cached copy
 * (https://phabricator.wikimedia.org/T38380). Relies on the CDN including
 * the query string in its cache key (Cloudflare's default).
 *
 * Wiring: the class swap runs from $wgExtensionFunctions, which MediaWiki
 * invokes after $wgLocalFileRepo has been fully built — whether by core's
 * defaults or by a storage extension. Both stock MediaWiki and
 * Extension:AWS (used here: stock LocalRepo class + AmazonS3 backend,
 * installed on the MediaWikiServices hook) end up with the "LocalRepo"
 * class, so the swap inherits the repo settings unchanged and only the
 * File class differs. The swap is skipped (and logged to the
 * VersionedFileUrls debug channel) if the repo class is anything else, so
 * an unrecognised repo degrades to unversioned URLs rather than a broken
 * repo.
 *
 * Old file versions are left untouched: their archive names already embed
 * an upload timestamp, so their URLs are inherently versioned. The same
 * reasoning excludes getArchiveThumbUrl(). Transcoded and stash file URLs
 * are also out of scope: no TimedMediaHandler here, and stash files are
 * not built via the repo factories.
 */

class VersionedLocalFile extends LocalFile {
    private function appendVersionToken( string $url ): string {
        // getSha1() triggers the file-row load; any caller actually
        // rendering a file has already loaded the row, so no queries are
        // added in practice. Missing files yield "" and skip the token.
        $sha1 = $this->getSha1();
        if ( !$sha1 || str_contains( $url, "?" ) ) {
            return $url;
        }
        return $url . "?" . substr( $sha1, 0, 5 );
    }

    /** @inheritDoc */
    public function getUrl() {
        return $this->appendVersionToken( parent::getUrl() );
    }

    /** @inheritDoc */
    public function getThumbUrl( $suffix = false ) {
        $url = parent::getThumbUrl( $suffix );
        // Without a suffix this is the thumbnail *directory* URL, which
        // callers extend with further path segments — a query string here
        // would corrupt those URLs.
        return $suffix === false ? $url : $this->appendVersionToken( $url );
    }
}

class VersionedLocalRepo extends LocalRepo {
    protected $fileFactory = [ VersionedLocalFile::class, "newFromTitle" ];
    protected $fileFactoryKey = [ VersionedLocalFile::class, "newFromKey" ];
    protected $fileFromRowFactory = [ VersionedLocalFile::class, "newFromRow" ];
}

$wgExtensionFunctions[] = static function () {
    global $wgLocalFileRepo;
    $class = $wgLocalFileRepo["class"] ?? null;
    // "LocalRepo" through REL1_43; MediaWiki\FileRepo\LocalRepo (with a
    // compat alias for the bare name) from REL1_44 on.
    if ( $class === "LocalRepo" || $class === "MediaWiki\\FileRepo\\LocalRepo" ) {
        $wgLocalFileRepo["class"] = VersionedLocalRepo::class;
    } else {
        wfDebugLog(
            "VersionedFileUrls",
            "Class swap skipped, unexpected repo class: " . var_export( $class, true )
        );
    }
};
