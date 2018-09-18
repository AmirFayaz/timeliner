// function filterTable($table,$target){
//     if ($target != 'all') {
//             $('#'+$table +' .row-filter').css('display', 'none').fadeOut('fast');
//             $('#'+$table +' .row-filter[data-status="' + $target + '"]').fadeIn('slow');
//     } else {
//             $('#'+$table +' .row-filter').css('display', 'none').fadeIn('slow');
//     }
// }
// function getFilterTableTarget($entry) {
//     var $target = $entry.data('target');
//     var $table = $entry.data('table');

//     console.log($target);
//     console.log($table);
//     filterTable($table,$target);
// }
// $(document).ready(function () {
                        
//     $('.btn-filter').on('click',function(){getFilterTableTarget($(this));} );

//     $('.btnPayDebtAddAction').on('click', function () {
//             var $loannumber = $(this).data('loannumber');
//             $next_tr = $(this).parent().parent().next();
//             $icon = $(this).children("i");
//             if($next_tr.hasClass('hidden')){
//                     $next_tr.fadeIn('fast').removeClass('hidden');
//                     $icon.removeClass().addClass('fas fa-angle-double-up')
//             }else{
//                     $next_tr.fadeOut('fast').addClass('hidden');
//                     $icon.removeClass().addClass('fas fa-angle-double-down')
//             }
//             });
// });