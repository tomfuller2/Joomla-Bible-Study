<?php
// @todo add header
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class biblestudyViewmp3 extends JView {

    function display($tpl = null) {
        $document = & JFactory::getDocument();
        $document->addStyleSheet(JURI::base() . 'media/com_biblestudyimport/assets/css/ui.css');
        $document->addStyleSheet(JURI::base() . 'media/com_biblestudyimport/assets/css/bsmImport.css');
        $document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
        $document->addScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.min.js');
        $document->addScript(JURI::base() . 'media/com_biblestudyimport/assets/js/bsmImport.js');

        JToolBarHelper::title(JText::_('Bible Study Import [mp3]'), 'generic.png');
        JToolBarHelper::editList('edit', 'Import');
        JToolBarHelper::help('biblestudyimport', true);

        // Get data from the model
        $model = & $this->getModel();

        $this->setLayout('form');
        $this->assignRef('items', $items);

        if (JRequest::getVar('preview')) {
            $tpl = "preview";

            foreach ($this->getAvailableTags(null, $model->getId3Sample()) as $key) {
                $tags[] = array('value' => $key, 'text' => $key);
            }
            array_shift($tags);
            array_unshift($tags, array('value' => null, 'text' => '- Use element from ID3 -'));

            $teachers = $model->getTeachers();
            $locations = $model->getLocations();
            $series = $model->getSeries();
            $topics = $model->getTopics();
            $types = $model->getTypes();
            $servers = $model->getServers();
            $folders = $model->getFolders();
            $podcasts = $model->getPodcast();
            $mimeTypes = $model->getMimeTypes();

            array_unshift($teachers, array('id' => null, 'teachername' => '- Use existing data -'));
            array_unshift($locations, array('id' => null, 'location_text' => '- Use existing data -'));
            array_unshift($series, array('id' => null, 'series_text' => '- Use existing data -'));
            array_unshift($topics, array('id' => null, 'topic_text' => '- Use existing data -'));
            array_unshift($types, array('id' => null, 'message_type' => '- Use existing data -'));
            array_unshift($servers, array('id' => null, 'server_name' => '- Use existing data -'));
            array_unshift($folders, array('id' => null, 'foldername' => '- Use existing data -'));
            array_unshift($podcasts, array('id' => null, 'title' => '- Use existing data -'));
            array_unshift($mimeTypes, array('id' => null, 'mimetext' => '- Use existing data -'));

            //Send to the view
            $this->assignRef('availableTags', $tags);
            $this->assignRef('availableTeachers', $teachers);
            $this->assignRef('availableLocations', $locations);
            $this->assignRef('availableSeries', $series);
            $this->assignRef('availableTopics', $topics);
            $this->assignRef('availableTypes', $types);
            $this->assignRef('availableServers', $servers);
            $this->assignRef('availableFolders', $folders);
            $this->assignRef('availablePodcasts', $podcasts);
            $this->assignRef('availableMimeTypes', $mimeTypes);

            //Send ID3 information
            $i = 0;
            foreach ($model->getId3Info() as $id3Element) {
                $document->addScriptDeclaration('var file' . $i . ' = ' . json_encode($id3Element) . ';');
                $i++;
            }
            $this->assignRef('id3Info', $model->getId3Info());
        }

        parent::display($tpl);
    }

    function getAvailableTags($parent = null, $id3Sample) {
        if (!is_array($keys)) {
            $keys = array();
        }
        foreach ($id3Sample as $sample => $value) {
            if (is_array($value)) {
                $keys = array_merge($keys, $this->getAvailableTags($parent . $sample . '.', $value));
            } else if ($parent != null) {
                $keys[] = $parent . $sample;
            } else {
                $keys[] = $sample;
            }
        }
        return $keys;
    }

}