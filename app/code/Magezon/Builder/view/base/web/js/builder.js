define([
	'angular',
	'formly',
	'Magezon_Builder/js/factories/config',
	'Magezon_Builder/js/services/magezonBuilder',
	'Magezon_Builder/js/services/url',
	'Magezon_Builder/js/services/modal',
	'Magezon_Builder/js/services/element',
	'Magezon_Builder/js/services/profile',
	'Magezon_Builder/js/services/form',
	'Magezon_Builder/js/services/editor',
	'Magezon_Builder/js/services/filter',
	'Magezon_Builder/js/services/history',
	'Magezon_Builder/js/controllers/base',
	'Magezon_Builder/js/controllers/list',
	'Magezon_Builder/js/controllers/modal',
	'Magezon_Builder/js/controllers/toolbar',
	'Magezon_Builder/js/directives/magezonBuilder',
	'Magezon_Builder/js/directives/modalElement',
	'Magezon_Builder/js/directives/resizable',
	'Magezon_Builder/js/directives/builderDirectiveList',
	'Magezon_Builder/js/builder/element/profile',
	'Magezon_Builder/js/directives/navigator',
	'Magezon_Builder/js/directives/elementIcon',
	'Magezon_Builder/js/directives/navigatorProfile',
	'Magezon_Builder/js/modules/contentEditable',
	'Magezon_Builder/js/modules/inlineEditor',
	'Magezon_Builder/js/modules/afterRender',
	'Magezon_Builder/js/directives/colorPicker',
	'Magezon_Builder/js/directives/elementActions',
	'Magezon_Builder/js/directives/elementResizable',
	'Magezon_Builder/js/directives/tooltip',
	'uiBootstrap',
	'dndLists',
	'angularSanitize',
	'dynamicDirective',
	'uiCodemirror',
	'uiSelect',
	'outsideClickDirective',
	'ngStats'
], function(
	angular,
	formly,
	configProvider,
	magezonBuilderSer,
	magezonBuilderUrlSer,
	magezonBuilderModalSer,
	elementManagerSer,
	profileManagerSer,
	formSer,
	editorSer,
	filterSer,
	historySer,
	baseController,
	listController,
	modalBaseControllerCtrl,
	toolbarControllerCtrl,
	magezonBuilderDir,
	modalElementDir,
	resizableDir,
	builderDirectiveListDir,
	profileDir,
	navigatorDir,
	elementIconDir,
	navigatorProfileDir,
	contentEditableDir,
	inlineEditorDir,
	afterRender,
	colorPicker,
	elementActions,
	elementResizable,
	tooltipDir
) {
	var builder = angular.module('magezonBuilder', ['formly', 'dndLists', 'ui.bootstrap', 'ngSanitize', 'dynamicDirective', 'ui.codemirror', 'ui.select', 'ngOutsideClick', 'angularStats']);

	builder.config(function($sceDelegateProvider) {
		$resourceUrlWhitelist = ['self','*://localhost/**','*://www.youtube.com/**', '*://player.vimeo.com/video/**'];
		$sceDelegateProvider.resourceUrlWhitelist($resourceUrlWhitelist.concat(window.builderConfig.resourceUrlWhitelist));
	});

	builder.run(function(dynamicDirectiveManager, magezonBuilderConfig, magezonBuilderService, magezonBuilderUrl, elementManager) {
		angular.forEach(magezonBuilderConfig.elements, function(element) {
			var type = elementManager.getElementType(element.type);
			require([element['element']], function(Directive) {
				dynamicDirectiveManager.registerDirective('mgzElement' + type, Directive, 'mgz');
			});
			require([element['navigator']], function(Directive) {
				dynamicDirectiveManager.registerDirective('mgzElementNavigator' + type, Directive, 'mgz');
			});
			if (element['toolbar']) {
				require([element['toolbar']], function(Directive) {
					dynamicDirectiveManager.registerDirective('mgzElementToolbar' + type, Directive, 'mgz');
				});
			}
		});
		angular.forEach(magezonBuilderConfig.directives, function(directive, name) {
			name = elementManager.getElementType(directive.type);
			if (directive['element']) {
				require([directive['element']], function(Directive) {
					dynamicDirectiveManager.registerDirective('mgzDirective' + name, Directive, 'mgz');
				});
			} else if (directive['templateUrl']) {
				function Directive() {
				 	return {
				 		replace: true,
				 		templateUrl: magezonBuilderUrl.getViewFileUrl(directive['templateUrl'])
				 	};
				}
				dynamicDirectiveManager.registerDirective('mgzDirective' + name, Directive, 'mgz');
			}
		});
		magezonBuilderService.directives = magezonBuilderConfig.directives;
	});

	// PROVIDER
	builder.provider('magezonBuilderConfig', configProvider);
	builder.service('magezonBuilderService', magezonBuilderSer);
	builder.service('magezonBuilderUrl', magezonBuilderUrlSer);
	builder.service('magezonBuilderModal', magezonBuilderModalSer);
	builder.service('elementManager', elementManagerSer);
	builder.service('profileManager', profileManagerSer);
	builder.service('historyManager', historySer);
	builder.service('magezonBuilderForm', formSer);
	builder.service('magezonBuilderEditor', editorSer);
	builder.service('magezonBuilderFilter', filterSer);

	// DIRECTIVE
	builder.directive('magezonBuilder', magezonBuilderDir);
	builder.directive('magezonBuilderModalElement', modalElementDir);
	builder.directive('magezonBuilderResizable', resizableDir);
	builder.directive('magezonBuilderDirectiveList', builderDirectiveListDir);
	builder.directive('magezonBuilderProfile', profileDir);
	builder.directive('magezonBuilderNavigator', navigatorDir);
	builder.directive('magezonBuilderElementIcon', elementIconDir);
	builder.directive('magezonBuilderNavigatorProfile', navigatorProfileDir);
	builder.directive('contentEditable', contentEditableDir);
	builder.directive('inlineEditor', inlineEditorDir);
	builder.directive('afterRender', afterRender);
	builder.directive('magezonBuilderColorPicker', colorPicker);
	builder.directive('magezonBuilderElementActions', elementActions);
	builder.directive('magezonBuilderElementResizable', elementResizable);
	builder.directive('mgzTooltip', tooltipDir);

	// CONTROLLER
	builder.controller('baseController', baseController);
	builder.controller('listController', listController);
	builder.controller('modalBaseController', modalBaseControllerCtrl);
	builder.controller('toolbarController', toolbarControllerCtrl);

	return builder;
});