<?php

namespace Atpro\mvc\Config\services;

class AtproUpload
{
    /**
     * @param string $formInputName {{ le name de l'input file (obligatoire ) }}
     * @param string $folder {{ le dossier de destination  (obligatoire ) sauf si image formdata}}
     * @param string|null $extensionsAllowed {{les extensions autoriser séparer par des virgules (facultatifs) }}
     * @return string | bool
     */
    public static function upload(string $formInputName, string $folder = '', string $extensionsAllowed = null): bool|string
    {
        if ($folder !== '') {
            //#region upload image
            $target_dir = $folder;
            $target_file = $target_dir . basename($_FILES[$formInputName]["name"]);
            $uploadOk = 1;
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            if (file_exists($target_file)) {
                $uploadOk = 0;
            }
            if ($extensionsAllowed != null) {
                $extensions_allowed = explode(',', $extensionsAllowed);
                if (!array_search($fileType, $extensions_allowed)) {
                    $uploadOk = 0;
                }
            }
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES[$formInputName]["tmp_name"], $target_file)) {
                    return basename($_FILES["fileToUpload"]["name"]);
                }
            }
            //#endregion
        } else {
            /** retour une image de type blob  */
            return base64_encode(file_get_contents(addslashes($_FILES[$formInputName]["tmp_name"])));
        }
        return false;
    }
}
