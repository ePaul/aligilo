<?php


die("vi ne rajtas uzi tion.");


/**
 * http://de.php.net/manual/de/function.readdir.php#77240
 */
function removeFilesIn($dirname)
{
    if (is_dir($dirname))
        {
            echo "$dirname is a directory.<br />\n";
            
            if ($handle = @opendir($dirname))
                {
                    while (($file = readdir($handle)) !== false)
                        {
                            if ($file != "." && $file != "..")
                                {
                                    echo "$file<br />\n";
                                    
                                    $fullpath = $dirname . '/' . $file;
                                    
                                    if (is_dir($fullpath))
                                        {
                                            removeFilesIn($fullpath);
                                            @rmdir($fullpath);
                                        }
                                    else
                                        {
                                            @unlink($fullpath);
                                        }
                                }
                        }
                    closedir($handle);
                }
            else {
                echo "ne eblas legi " . $dirname . ".<br/>\n";
            }
        }
}

removeFilesIn("/home/groups/aligilo/htdocs/generita_dokumentajxo");

echo "Finita!";

?>