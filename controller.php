<?php
    include_once ('\library\crawler.php');
    include_once ('\library\database.php');
    
    if($_REQUEST['task'] === 'crawl'){
        $crawler = new WikiCrawler();
        $document = $crawler->getPageDocument($_REQUEST['url'], 'name');
        $textNodes = $crawler->getTextNodes($document);

        echo $crawler->serialize($textNodes);
    }
    
    if($_REQUEST['task'] === 'get_word'){
        $database = new database_helper();
        echo $database->get_word($_REQUEST['word']);
    }
        
        if($_REQUEST['task'] === 'insert_url'){
        $database = new database_helper();
        echo $database->insert_url($_REQUEST['url'],$_REQUEST['user']);
    }
        
        if($_REQUEST['task'] === 'insert_word'){
        $database = new database_helper();
        echo $database->insert_word($_REQUEST['word'],$_REQUEST['link']);
    }
        
        if($_REQUEST['task'] === 'word_activate'){
        $database = new database_helper();
        echo $database->word_activate($_REQUEST['word']);
    }
        
        if($_REQUEST['task'] === 'word_deactivate'){
        $database = new database_helper();
        echo $database->word_deactivate($_REQUEST['word']);
    }
        
        if($_REQUEST['task'] === 'admin_activate'){
        $database = new database_helper();
        echo $database->admin_activate($_REQUEST['benutzer']);
    }
        
        if($_REQUEST['task'] === 'user_delete'){
        $database = new database_helper();
        echo $database->user_delete($_REQUEST['benutzer']);
    }
        
        if($_REQUEST['task'] === 'show_common_words'){
        $database = new database_helper();
        echo $database->show_common_words();
    }
        
        if($_REQUEST['task'] === 'show_active_users'){
        $database = new database_helper();
        echo $database->show_active_users();
    }
?>
