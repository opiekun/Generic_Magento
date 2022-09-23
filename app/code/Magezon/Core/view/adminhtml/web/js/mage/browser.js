define([
 	"jquery",
 	"Magento_Ui/js/modal/prompt",
 	"Magento_Ui/js/modal/confirm",
 	"Magento_Ui/js/modal/alert",
 	"Magento_Ui/js/modal/modal",
 	"jquery/jstree/jquery.jstree",
 	"mage/mage",
 	"mage/adminhtml/browser"
 	], function($, prompt, confirm, alert){

 		window.MgzMediabrowserUtility = {
 			windowId: 'modal_dialog_message',
 			getMaxZIndex: function() {
 				var max = 0, i;
 				var cn = document.body.childNodes;
 				for (i = 0; i < cn.length; i++) {
 					var el = cn[i];
 					var zIndex = el.nodeType == 1 ? parseInt(el.style.zIndex, 10) || 0 : 0;
 					if (zIndex < 10000) {
 						max = Math.max(max, zIndex);
 					}
 				}
 				return max + 10;
 			},
 			openDialog: function(url, width, height, title, options) {
 				var windowId = this.windowId,
 				content = '<div class="popup-window magento-message" "id="' + windowId + '"></div>',
 				self = this;

 				if (this.modal) {
 					this.modal.html($(content).html());
 					if (options) {
 					  this.modal.modal('option', 'closed', options.closed);
 					}
 				} else {
 					this.modal = $(content).modal($.extend({
 						title:  title || 'Insert File...',
 						modalClass: 'magento',
 						type: 'slide',
 						buttons: []
 					}, options));
 				}
 				this.modal.modal('openModal');
 				$.ajax({
 					url: url,
 					type: 'get',
 					context: $(this),
 					showLoader: true

 				}).done(function(data) {
 					self.modal.html(data).trigger('contentUpdated');
 				});
 			},
 			closeDialog: function() {
 				this.modal.modal('closeModal');
 			}
 		};

 		$.widget("mage.mgzmediabrowser", $.mage.mediabrowser, {
 			eventPrefix: "mgzmediabrowser",
 			insert: function(event) {
 				var fileRow = $(event.currentTarget);

 				if (!fileRow.prop('id')) {
 					return false;
 				}
 				var targetEl = this.getTargetElement();

 				if (!targetEl.length) {
 					MgzMediabrowserUtility.closeDialog();
 					throw "Target element not found for content update";
 				}
 				return $.ajax({
 					url: this.options.onInsertUrl,
 					data: {
 						filename: fileRow.attr('id'),
 						node: this.activeNode.id,
 						store: this.options.storeId,
 						as_is: targetEl.is('textarea') ? 1 : 0,
 						form_key: FORM_KEY
 					},
 					context: this,
 					showLoader: true
 				}).done($.proxy(function(data) {

 					if (targetEl.is('textarea')) {
 						this.insertAtCursor(targetEl.get(0), data);
 					} else {
 						targetEl.val(data);
 					}
 					MgzMediabrowserUtility.closeDialog();
 					targetEl.focus();
 					jQuery(targetEl).change();
 				}, this));
 			},

			/**
			 * Find document target element in next order:
			 *  in acive file browser opener:
			 *  - input field with ID: "src" in opener window
			 *  - input field with ID: "href" in opener window
			 *  in document:
			 *  - element with target ID
			 *
			 * return {HTMLElement|null}
			 */
			getTargetElement: function () {
				var opener, targetElementId;
				if (typeof wysiwyg != 'undefined' && wysiwyg.get(this.options.targetElementId)) {
					opener = this.getMediaBrowserOpener() || window;
					targetElementId = tinyMceEditors.get(this.options.targetElementId).getMediaBrowserTargetElementId();
					return $(opener.document.getElementById(targetElementId));
				}
				return $('#' + this.options.targetElementId);
			}

 		});

 		return $.mage.mgzmediabrowser;
 	});
