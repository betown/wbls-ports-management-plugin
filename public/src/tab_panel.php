<div class="tab-pane fade <?php if(!$activePanePrinted){echo 'show active';}?>"
  id="<?php echo sanitize_title_with_dashes($data['name']).'-content';?>" role="tabpanel"
  aria-labelledby="<?php echo sanitize_title_with_dashes($data['name']).'-tab';?>">
  <?php $activePanePrinted = true; ?>
  <div class="terminal-information">
    <header class="terminal-header d-flex justify-content-between align-items-center">
      <h2><?php echo $data['name'];?></h2>
      <div class="truck-status">
        <ul class="list-unstyled">
          <?php
              switch ($data['truck']) {
                case 1:
                  echo '<li class="red active">stop</li>';
                break;
                case 2:
                  echo '<li class="yellow">wait</li>';
                break;
                case 3:
                  echo '<li class="green">pass</li>';
                break;
              }
            ?>
        </ul>
      </div>
    </header>
    <div class="main-content">
      <section class="port-information">
        <h4>Port Information</h4>
        <?php
          $restrictions_indexes = array('max_loa', 'max_beam', 'depth_alongside', 'max_draft','airdraft','storage_capacity','loading_rates','discharging_rates','method_of_loading','method_of_discharging','frontage_dolphins','tide_restriction','night_arrival_sailing_restriction','average_congestion','bunkers','water_availability','garbage_collection_compulsory','crushing_capacity');
          $restrictions_headers = array(
            'max_loa' => 'Max Loa',
            'max_beam' => 'Max Beam',
            'depth_alongside' => 'Depth Alongside',
            'max_draft' => 'Max Draft',
            'airdraft' => 'Airdraft',
            'storage_capacity' => 'Storage Capacity',
            'loading_rates' => 'Loading Rates',
            'discharging_rates' => 'Discharging Rates',
            'method_of_loading' => 'Method of Loading',
            'method_of_discharging' => 'Method of Discharging',
            'frontage_dolphins' => 'Frontage Dolphins',
            'tide_restriction' => 'Tide Restriction',
            'night_arrival_sailing_restriction' => 'Night arrival / Sailing Restriction',
            'average_congestion' => 'Average Congestion',
            'bunkers' => 'Bunkers',
            'water_availability' => 'Water Availability',
            'garbage_collection_compulsory' => 'Garbage Collection Compulsory',
            'crushing_capacity' => 'Crushing Capacity'
          );
        ?>
        <div class="terminal__data-table port-restrictions">
          <?php foreach($restrictions_indexes as $index):
            if(!empty($data[$index])):?>
            <div class="terminal__table-block">
              <div class="table-block__header">
                <?php echo $restrictions_headers[$index]; ?>
              </div>
              <div class="table-block__content">
                <?php echo $data[$index]; ?>
              </div>
            </div>

          <?php endif;
          endforeach;?>
        </div>

        <div class="terminal__data-table daily-draft-waiting-time">
          <?php
            $terminal_data_indexes = array('daily_draft_mts','daily_draft_feets','water_density','waiting_time','waiting_time_projected');
            $terminal_data_headers = array(
            'daily_draft_mts' => 'Daily Draft Mts',
            'daily_draft_feets' => 'Daily Draft Feets',
            'water_density' => 'Water Density',
            'waiting_time' => 'Waiting Time',
            'waiting_time_projected' => 'Waiting Time Projected');
          ?>
          <?php foreach($terminal_data_indexes as $index): ?>
            <div class="terminal__table-block">
              <div class="table-block__header">
                <?php echo $terminal_data_headers[$index]; ?>
              </div>
              <div class="table-block__content">
                <?php echo !empty($data[$index])? $data[$index] : 'N/A'; ?>
              </div>
            </div>
          <?php endforeach;?>
        </div>

      </section>
      <?php if (!empty($data['lineup'])): ?>
      <section class="line-up">
        <h4>Line Up</h4>
        <?php foreach ($data['lineup'] as $lineup_item):?>
        <article class="vessel">
          <header class="d-flex justify-content-between align-items-end">
            <h5 class="green"><?php echo $lineup_item['vessel_name'];?></h5>
            <h6 class="mid-grey">type: <?php echo strtoupper($lineup_item['vessel_type']);?></h6>
          </header>
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <tr>
                <th scope="col">eta</th>
                <th scope="col">Comodity</th>
                <th scope="col">Quantity</th>
                <th scope="col">Operation</th>
                <th scope="col">Destination</th>
              </tr>
              <tr>
                <td><?php echo $lineup_item['eta'];?></td>
                <td><?php echo $lineup_item['comodity'];?></td>
                <td><?php echo $lineup_item['quantity'];?></td>
                <td><?php echo $lineup_item['operation'];?></td>
                <td><?php echo $lineup_item['destination'];?></td>
              </tr>
              </tbody>
            </table>
          </div>
        </article>
        <?php endforeach; ?>
      </section>
      <?php endif;?>
    </div>

  </div>
</div>