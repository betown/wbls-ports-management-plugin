jQuery(function($) {
  function goToNextTerminal(evt) {
    var $modal = $(evt.currentTarget).parents('.modal');
    var terminalsData = getTerminalsData($modal);

    terminalsData.terminalList.hide();

    if(terminalsData.currentTerminal == terminalsData.terminalCount) {
      terminalsData.terminalList.eq(0).show();
    } else {
      terminalsData.terminalList.eq(terminalsData.currentTerminal + 1).show();
    }
  }

  function goToPrevTerminal(evt) {
    var $modal = $(evt.currentTarget).parents('.modal');
    var terminalsData = getTerminalsData($modal);

    terminalsData.terminalList.hide();

    if(terminalsData.currentTerminal == 0) {
      terminalsData.terminalList.last().show();
    } else {
      terminalsData.terminalList.eq(terminalsData.currentTerminal - 1).show();
    }
  }

  function getTerminalsData($modal) {
    var $terminalList = $('.modal-body', $modal);
    return {
      terminalList: $terminalList,
      currentTerminal: getActiveTerminalIndex($terminalList),
      // Set the conter to 0 based index count
      terminalCount: $terminalList.length - 1
    }
  }

  function getActiveTerminalIndex($terminalList) {
    var current_index;

    $terminalList.each(function(index, el) {
      if ($(el).is(':visible')) {
        console.log(index);
        current_index = index;
      };
    });

    return current_index;
  };

  function openModalTerminal(evt) {
    var $this = $(evt.currentTarget);
    var terminalId = $this.data('terminal');
    var $terminal = $('.modal-body[data-terminal="'+terminalId+'"]');


    $terminal.show().siblings('.modal-body').hide();
  }

  $('[data-next-terminal]').on('click', goToNextTerminal);
  $('[data-prev-terminal]').on('click', goToPrevTerminal);
  $('[data-terminal]').on('click', openModalTerminal);
});