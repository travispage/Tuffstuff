/**
 * Copyright (c) 2013 Luigi Cavalieri
 * Distributed under the same license as the SiteTree package.
 * --------------------------------------------------------------- */


function SiteTreeSetting( id ) {
	this.id		   = id;
	this._target   = document.getElementById( id );
	this._jqTarget = null;
	this._row	   = null;
}

SiteTreeSetting.prototype.value = function() {
	if ( this._target )
		return this._target.value;
	
	return null;
};

SiteTreeSetting.prototype.disable = function( disable ) {
	if (! this._target )
		return false;
		
	if ( typeof disable == 'undefined' )
		disable = true;
	
	this._target.disabled = disable;
};
	
SiteTreeSetting.prototype.bindEvent = function( event, handler ) {
	if (! this._jqTarget )
		this._jqTarget = jQuery( this._target );
		
	this._jqTarget.on( event, handler );
};
	
SiteTreeSetting.prototype.isChecked = function() {
	if ( this._target )
		return this._target.checked;
		
	return null;
};
	
SiteTreeSetting.prototype.hide = function( hide ) {
	if (! this._target )
		return false;
		
	if (! this._row )
		this._row = this._target.parentNode.parentNode;
	
	if ( hide || ( typeof hide === 'undefined' ) )
		this._row.style.display = 'none';
	else
		this._row.style.display = 'table-row';
};

SiteTreeSetting.prototype.toggle = function() {
	if (! this._target )
		return false;
		
	if (! this._row )
		this._row = this._target.parentNode.parentNode;
		
	if ( this._row.style.display == 'none' )
		this._row.style.display = 'table-row';
	else
		this._row.style.display = 'none';
};


var SiteTree = (function($) {
	var _dialog = {
		isOpen: false,
	
		open: function() {
			if (! _dialog.isOpen ) {
				_dialog.isOpen = true;
				_dialog.view.style.display = 'block';
			}
			
			return false;
		},
		
		close: function() {
			_dialog.isOpen = false;
			_dialog.view.style.display = 'none';
			
			return false;
		},
	};
	
	var _pingInfo = {
		mouseIsOn: false,
		isVisible: false,
	
		show: function() {
			_pingInfo.mouseIsOn = true;
			
			if (! _pingInfo.isVisible ) {
				_pingInfo.isVisible = true;
				_pingInfo.view.style.display = 'block';
			}
		},
		
		hide: function() {
			_pingInfo.mouseIsOn = false;
		
			setTimeout(function(){
				if (! _pingInfo.mouseIsOn ) {
					_pingInfo.isVisible = false;
					_pingInfo.view.style.display = 'none';
				}
			}, 800);
		},
	};
	
	return {
		init: function( page_id, l10n ) {
			switch ( page_id ) {
				case 'sitetree-dashboard':
					var select_page		= document.getElementById( 'page-for-sitemap' );
					var disable_btn		= document.getElementById( 'sitetree-disable-tb-btn' );
					var cancel_ping_btn = document.getElementById( 'sitetree-cancel-ping-btn' );
					
					_dialog.view   = document.getElementById( 'sitetree-about-view' );
					_pingInfo.view = document.getElementById('sitetree-ping-info');
					
					$('#sitetree-info-btn').bind( 'click', _dialog.open );
					$('#sitetree-about-view-close').bind( 'click', _dialog.close );
					$('#sitetree-ping').hover( _pingInfo.show, _pingInfo.hide );
					
					if ( disable_btn )
						$( disable_btn ).bind( 'click', function(){ return confirm( l10n.warnDisable ); } );
						
					if ( cancel_ping_btn )
						$( cancel_ping_btn ).bind( 'click', function(){ return confirm( l10n.warnCancelPing ); } );
						
					if ( select_page && ( select_page.value === '0' ) ) {
						var enable_archive = $('input.sitetree-primary-btn');
						
						enable_archive			= enable_archive[enable_archive.length - 1];	
						enable_archive.disabled = true;
						
						$( select_page ).change( function() { enable_archive.disabled = ( this.value === '0' ); });
					}
					break;
				case 'sitetree-html5':
					var settings = {};
					var ids		 = ['post-groupby',	'post-orderby', 'post-category-label', 'page-list-style',
									'page-depth', 'authors-show-avatar', 'authors-avatar-size'];
					
					for ( var i = 0; i < ids.length; i++ )
						settings[ ids[i].replace( /-/g, '_' ) ] = new SiteTreeSetting( ids[i] );
					
					// Initialise state
					if (! settings.authors_show_avatar.isChecked() )
						settings.authors_avatar_size.hide();
					
					if ( settings.page_list_style.value() === '0' )
						settings.page_depth.hide();
						
					if ( settings.post_groupby.value() == 'date' ) {
						settings.post_category_label.hide();
						settings.post_orderby.hide();
					}
					else if ( settings.post_groupby.value() != 'category' )
						settings.post_category_label.hide();
					
					// Events binding
					settings.authors_show_avatar.bindEvent( 'click', function() { settings.authors_avatar_size.toggle(); } );
					
					settings.page_list_style.bindEvent( 'change', function() {
						settings.page_depth.hide( this.value === '0' ); }
					);
					
					settings.post_groupby.bindEvent( 'change', function() {
						settings.post_orderby.hide( this.value == 'date' );
						settings.post_category_label.hide( this.value != 'category' );
					});
					break;
			}	
		}
	};
})(jQuery);