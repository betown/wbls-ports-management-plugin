<div class="modal-body" data-terminal="<?php echo sanitize_title_with_dashes($terminal); ?>" <?php if($terminal_content_printed == true){ echo 'style="display:none"'; }; ?>>
  <div class="row mx-0">
    <div class="col-lg-3 col-12 px-0">
      <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
        <?php
        $activePanePrinted = false;
        if(array_key_exists('docks_list', $content_data)):
          foreach ($content_data['docks_list'] as $data):?>
            <a class="nav-link <?php if(!$activePanePrinted){ echo 'active'; }?>"
              id="<?php echo sanitize_title_with_dashes($data['name']) . '-tab';?>" data-toggle="pill"
              role="tab" href="#<?php echo sanitize_title_with_dashes($data['name']) . '-content';?>"
              aria-controls="<?php echo sanitize_title_with_dashes($data['name']).'-content';?>"
              aria-selected="true"><?php echo $data['name'];?></a>
        <?php
          $activePanePrinted = true;
          endforeach;
        else: ?>
          <a class="nav-link active"
            id="<?php echo sanitize_title_with_dashes($content_data['name']) . '-tab';?>" data-toggle="pill"
            role="tab" href="#<?php echo sanitize_title_with_dashes($content_data['name']) . '-content';?>"
            aria-controls="<?php echo sanitize_title_with_dashes($content_data['name']).'-content';?>"
            aria-selected="true"><?php echo $content_data['name'];?></a>
        <?php
        endif;
        ?>
      </div>
    </div>
    <div class="col-lg-9 col-12  px-0">
      <div class="tab-content" id="v-pills-tabContent">
        <?php
          $activePanePrinted = false;
          if(array_key_exists('docks_list', $content_data)){
            foreach ($content_data['docks_list'] as $data){
              require 'tab_panel.php';
            }
          } else {
            $data = $content_data;
            require 'tab_panel.php';
          }
        ?>
      </div>
    </div>
  </div>
</div>
<?php
  $terminal_content_printed = true;
?>