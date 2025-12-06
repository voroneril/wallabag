<?php

namespace Wallabag\CoreBundle\Helper;

/**
 * Hash URLs for privacy and performance.
 */
class AutorEncodingFixer
{
    /**
     * Hash the given url using the given algorithm.
     * Hashed url are faster to be retrieved in the database than the real url.
     *
     * @param Entry $entry
     * @param EntityManagerInterface $em
     *
     * @return bool
     */
    public static function fix(Entry $entry, EntityManagerInterface $em)
    {
        $published_by = $entry->getPublishedBy();
        $need_update = false;
        if ($published_by) {
            if (is_array($published_by) && sizeof($published_by) > 0) {
                foreach ($published_by as $author_id => $author) {
                    if (strpos($author, 'Ð') !== false) {
                        $need_update = true;
                        $utf8_string = mb_convert_encoding($author, 'Windows-1252', 'UTF-8');
                        $published_by[$author_id] = $utf8_string;
                    }
                }
                if ($need_update) {
                    $entry->setPublishedBy($published_by);
                    $em->flush();
                }
            }
        }
        return $need_update;
    }
}
