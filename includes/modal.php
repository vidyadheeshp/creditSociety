<?php
    // Author : Anand V Deshpande, KLS GIT, NAIN
    // Date Written : 
    //Modal Form    //Can be called from any HTML
    $ModalHeading = "";
    $ModalBody    = "";
    $ModalFooter  = "";
?>
<!--
    <div class="modal-dialog modal-sm" or "modal-lg"   modal-dialog-centered >
-->




<div class="modal fade" id="avdModal" tabindex="-1" role="dialog" aria-labelledby="avdModalLabel" aria-hidden="true"  style="color: brown;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h5 class="modal-title" id="avdModalLabel">
                <?php echo $ModalHeading; ?>    
				</h5>
            </div>
            <div class="modal-body table-responsive" id="modalbody_html">
                <?php echo $ModalBody; ?> 
            </div>
            <div class="modal-footer" id="modalfooter_html">
                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                <?php echo $ModalFooter; ?>
            </div>
        </div>
    </div>
</div>
