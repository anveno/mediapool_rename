<?php

rex_extension::register('MEDIA_FORM_EDIT', ['rex_mediapool_rename', 'deleteMetaInfo'], rex_extension::LATE );
rex_extension::register('MEDIA_UPDATED', ['rex_mediapool_rename', 'processUpdatedMedia'], rex_extension::LATE );
