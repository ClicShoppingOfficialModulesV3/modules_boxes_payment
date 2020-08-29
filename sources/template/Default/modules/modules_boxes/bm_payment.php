<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class bm_payment {
    public $code;
    public $group;
    public string $title;
    public string $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;
    public $pages;

    public function  __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_boxes_payment_title');
      $this->description = CLICSHOPPING::getDef('module_boxes_payment_description');

      if (defined('MODULE_BOXES_PAYMENT_STATUS')) {
        $this->sort_order = MODULE_BOXES_PAYMENT_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_PAYMENT_STATUS == 'True');
        $this->pages = MODULE_BOXES_PAYMENT_DISPLAY_PAGES;
        $this->group = ((MODULE_BOXES_PAYMENT_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    public function  execute() {

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Service = Registry::get('Service');
      $CLICSHOPPING_Banner = Registry::get('Banner');

      if (!empty(MODULE_BOXES_PAYMENT_IMAGE_BANK) && !empty(MODULE_BOXES_PAYMENT_IMAGE_SHIPPING)) {
        $logo_banner_payment = '';

        if ($CLICSHOPPING_Service->isStarted('Banner')) {
          if ($banner = $CLICSHOPPING_Banner->bannerExists('dynamic',  MODULE_BOXES_PAYMENT_BANNER_GROUP)) {
            $logo_banner_payment = $CLICSHOPPING_Banner->displayBanner('static', $banner) . '<br /><br />';
          }
        }

        $data = '<!-- boxe payment / shipping start-->' . "\n";

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules('/modules_boxes/content/payment'));

        $data .= ob_get_clean();

        $data .='<!-- Boxe categories end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($data, $this->group);
      }
    }

    public function  isEnabled() {
      return $this->enabled;
    }

    public function  check() {
      return defined('MODULE_BOXES_PAYMENT_STATUS');
    }

    public function  install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_BOXES_PAYMENT_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please choose where the boxe must be displayed',
          'configuration_key' => 'MODULE_BOXES_PAYMENT_CONTENT_PLACEMENT',
          'configuration_value' => 'Right Column',
          'configuration_description' => 'Choose where the boxe must be displayed',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Left Column\', \'Right Column\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the group where the banner belongs',
          'configuration_key' => 'MODULE_BOXES_PAYMENT_BANNER_GROUP',
          'configuration_value' => SITE_THEMA.'_boxe_payment',
          'configuration_description' => 'Please indicate the group where the banner belongs <br /> <br /> <strong> Note: </strong> <br /> <i> The group will be indicated when creating the banner in the Marketing section / Banner management </i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer l\'url du site de banque effectuant le paiement principal',
          'configuration_key' => 'MODULE_BOXES_PAYMENT_URL_BANK',
          'configuration_value' => '',
          'configuration_description' => 'Veuillez insérer une url du type http://www.mabanque.com',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer le nom de l\'image du logo de la banque effectuant le paiement principal',
          'configuration_key' => 'MODULE_BOXES_PAYMENT_IMAGE_BANK',
          'configuration_value' => '',
          'configuration_description' => 'Les logos se trouvent dans la section image du site :  image/logos/payment/',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer l\'url du site du fournisseur effectuant l\'expédition principale du produit',
          'configuration_key' => 'MODULE_BOXES_PAYMENT_URL_SHIPPING',
          'configuration_value' => '',
          'configuration_description' => 'Veuillez insérer un url du type http://www.expediteur.com',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer le nom de l\'image du logo de du fournisseur effectuant l\'expédition principale du produit',
          'configuration_key' => 'MODULE_BOXES_PAYMENT_IMAGE_SHIPPING',
          'configuration_value' => '',
          'configuration_description' => 'Les logos se trouvent dans la section image du site :  image/logos/shipping/',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );



      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BOXES_PAYMENT_SORT_ORDER',
          'configuration_value' => '120',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate where boxing should be displayed',
          'configuration_key' => 'MODULE_BOXES_PAYMENT_DISPLAY_PAGES',
          'configuration_value' => 'all',
          'configuration_description' => 'Select the pages where boxing must be present.',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => 'clic_cfg_set_select_pages_list',
          'date_added' => 'now()'
        ]
      );

    }

    public function  remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function  keys() {
      return array('MODULE_BOXES_PAYMENT_STATUS',
                   'MODULE_BOXES_PAYMENT_CONTENT_PLACEMENT',
                   'MODULE_BOXES_PAYMENT_BANNER_GROUP',
                   'MODULE_BOXES_PAYMENT_URL_BANK',
                   'MODULE_BOXES_PAYMENT_IMAGE_BANK',
                   'MODULE_BOXES_PAYMENT_URL_SHIPPING',
                   'MODULE_BOXES_PAYMENT_IMAGE_SHIPPING',
                   'MODULE_BOXES_PAYMENT_SORT_ORDER',
                   'MODULE_BOXES_PAYMENT_DISPLAY_PAGES'
                  );
    }
  }
