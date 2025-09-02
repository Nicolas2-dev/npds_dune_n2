<?php

namespace Modules\Upload\Support;

use App\Library\Security\Hack;
use App\Support\FileManagement;
use Modules\Upload\Support\UploadStr;


class UploadMineRender
{

    /**
     * Renders a text file in a styled list-group item.
     *
     * @param array $attribut {
     *     @type string $name    The name of the file.
     *     @type string $path    The filesystem path to the text file.
     *     @type string $visible Additional visibility info (e.g., flags, notes).
     * }
     * @return string HTML output for the text file.
     */
    public static function renderText(array $attribut): string
    {
        $name    = $attribut['name'];
        $path    = $attribut['path'];
        $visible = $attribut['visible'];

        $content = str_replace("\\", "\\\\", htmlspecialchars(join('', file($path)), ENT_COMPAT | ENT_HTML401, 'UTF-8'));
        $content = UploadStr::wordWrap($content);

        return  '<div class="list-group-item flex-column align-items-start">
                <div class="py-2 mb-2">
                    <code>' . $name . $visible . '</code>
                </div>
                <div style="width:100%;">
                    <pre>' . $content . '</pre>
                </div>
            </div>';
    }

    /**
     * Renders an HTML file safely inside a styled table.
     *
     * @param array $attribut {
     *     @type string $name    The name of the HTML file.
     *     @type string $path    The filesystem path to the HTML file.
     *     @type string $visible Additional visibility info.
     * }
     * @return string HTML output for the HTML file.
     */
    public static function renderHtml(array $attribut): string
    {
        $name    = $attribut['name'];
        $path    = $attribut['path'];
        $visible = $attribut['visible'];

        // $content = Hack::removeHack(join('', file ($path)));        
        $content = UploadStr::scrHtml(join('', file($path)));
        $content = nl2br($content);
        $content = UploadStr::wordWrap($content);

        return '<table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="background-color: #000000;">
                    <table border="0" cellpadding="5" cellspacing="1" width="100%">
                        <tr>
                            <td align="center" style="background-color: #cccccc;">' . $name . $visible . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #ffffff;">' . $content . '</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </table>';
    }

    /**
     * Renders a downloadable link with file size, type, and counter badge.
     *
     * @param array $attribut {
     *     @type string $name     The file name.
     *     @type string $url      The URL to the file.
     *     @type string $type     The MIME type or file type.
     *     @type int    $size     File size in bytes.
     *     @type string $visible  Visibility or extra info.
     *     @type int    $compteur Download counter.
     * }
     * @return string HTML output for the download link.
     */
    public static function renderLink(array $attribut): string
    {
        $name       = $attribut['name'];
        $url        = $attribut['url'];
        $type       = $attribut['type'];
        $size       = $attribut['size'];
        $visible    = $attribut['visible'];
        $compteur   = $attribut['compteur'];

        $Fichier = new FileManagement;
        $size    = $Fichier->fileSizeFormat($size, 1);

        $icon = UploadIcon::attIcon($name);

        return '<a class="list-group-item list-group-item-action d-flex justify-content-start align-items-center" href="' . $url . '" target="_blank" >
                ' . $icon . '
                <span title="' . upload_translate("Télécharg.") . ' ' . $name . ' (' . $type . ' - ' . $size . ')" data-bs-toggle="tooltip" style="font-size: .85rem;" class="ms-2 n-ellipses">
                    <strong>&nbsp;' . $name . '</strong>
                </span>
                <span class="badge bg-secondary ms-auto" style="font-size: .75rem;">
                    ' . $compteur . ' &nbsp;<i class="fa fa-lg fa-download"></i>
                </span>
                <br />
                <span align="center">' . $visible . '</span>
            </a>';
    }

    /**
     * Renders an image inside a list-group item with a clickable popup.
     *
     * @param array $attribut {
     *     @type string $name    The image name.
     *     @type string $url     The URL to the image.
     *     @type string $visible Optional visibility info.
     * }
     * @return string HTML output for the image.
     */
    public static function renderImg(array $attribut): string
    {
        $name    = $attribut['name'];
        $url     = $attribut['url'];
        $visible = $attribut['visible'];

        return '<div class="list-group-item list-group-item-action flex-column align-items-start">
                    <code>' . $name . '</code>
                    <a href="javascript:void(0);" onclick="window.open(\'' . $url . '\', \'fullsizeimg\', \'menubar=no,location=no,directories=no,status=no,copyhistory=no,height=600,width=800,toolbar=no,scrollbars=yes,resizable=yes\');">
                        <img src="' . $url . '" alt="' . $name . '" style="width: 100%; height:auto;" loading="lazy">' . $visible . '
                    </a>
                </div>';
    }

    /**
     * Renders a Shockwave Flash (SWF) object.
     *
     * @param array $attribut {
     *     @type string $url     The URL to the SWF file.
     *     @type string $path    The local path to the SWF file (for size extraction).
     *     @type string $visible Optional visibility info.
     * }
     * @return string HTML output for the SWF object.
     */
    public static function renderShockwaveFlash(array $attribut): string
    {
        $url     = $attribut['url'];
        $visible = $attribut['visible'];

        $size = @getImageSize($attribut['path']);
        $size = UploadStr::verifSize($size);

        return '<p align="center">
                    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4\,0\,2\,0" ' . $size . '>
                        <param name="quality" value="high">
                        <param name="SRC" value="' . $url . '">
                        <embed src="' . $url . '" quality="high" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" ' . $size . '></embed>
                    </object>' . $visible . '
                </p>';
    }

    /**
     * Renders a video element with a source.
     *
     * @param array $attribut {
     *     @type string $name The video name.
     *     @type string $url  The URL to the video file.
     * }
     * @return string HTML output for the video.
     */
    public static function renderVideo(array $attribut): string
    {
        $name    = $attribut['name'];
        $url     = $attribut['url'];

        return '<div class="list-group-item list-group-item-action flex-column align-items-start">
                <code>' . $name . '</code>
                <div>
                    <video playsinline preload="metadata" muted controls width="100%" height="auto";>
                        <source src="' . $url . '" type="video/mp4">
                    </video>
                </div>
            </div>';
    }

    /**
     * Renders an audio element with a source.
     *
     * @param array $attribut {
     *     @type string $name The audio name.
     *     @type string $url  The URL to the audio file.
     * }
     * @return string HTML output for the audio.
     */
    public static function renderAudio(array $attribut): string
    {
        $name    = $attribut['name'];
        $url     = $attribut['url'];

        return '<div class="list-group-item list-group-item-action flex-column align-items-start">
                <code>' . $name . '</code>
                <div>
                    <audio controls src="' . $url . '"></audio><br />
                </div>
            </div>';
    }
}
