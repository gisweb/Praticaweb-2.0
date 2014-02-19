  (function( $ ) {
      $.widget("artistan.loading", $.ui.dialog, {
        options: {
            // your options
            spinnerClassSuffix: 'spinner',
            spinnerHtml: 'In caricamento',// allow for spans with callback for timeout...
            maxHeight: false,
            maxWidth: false,
            minHeight: 80,
            minWidth: 220,
            height: 80,
            width: 220,
            modal: true
        },

        _create: function() {
            $.ui.dialog.prototype._create.apply(this);
            // constructor
            $(this.uiDialog).children('*').hide();
            var self = this,
            options = self.options;
            self.uiDialogSpinner = $('.ui-dialog-content',self.uiDialog)
                .html(options.spinnerHtml)
                .addClass('ui-dialog-'+options.spinnerClassSuffix);
        },
        _setOption: function(key, value) {
            var original = value;
            $.ui.dialog.prototype._setOption.apply(this, arguments);
            // process the setting of options
            var self = this;

            switch (key) {
                case "innerHeight":
                    // remove old class and add the new one.
                    self.uiDialogSpinner.height(value);
                    break;
                case "spinnerClassSuffix":
                    // remove old class and add the new one.
                    self.uiDialogSpinner.removeClass('ui-dialog-'+original).addClass('ui-dialog-'+value);
                    break;
                case "spinnerHtml":
                    // convert whatever was passed in to a string, for html() to not throw up
                    self.uiDialogSpinner.html("" + (value || '&#160;'));
                    break;
            }
        },
        _size: function() {
            $.ui.dialog.prototype._size.apply(this, arguments);
        },
        // other methods
        loadStart: function(newHtml){
            if(typeof(newHtml)!='undefined'){
                this._setOption('spinnerHtml',newHtml);
            }
            this.open();
        },
        loadStop: function(){
            this._setOption('spinnerHtml',this.options.spinnerHtml);
            this.close();
        }
    });
    /*$.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          //autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Mostra tutti gli elementi" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn't match any item" )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.data( "ui-autocomplete" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });*/
  })( jQuery );
  
  $.widget( "custom.catcomplete", $.ui.autocomplete, {
    _renderMenu: function( ul, items ) {
       console.log(ul);  
      var that = this,
        currentCategory = "";
      $.each( items, function( index, item ) {
         
        if ( item.category != currentCategory ) {
          ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
          currentCategory = item.category;
        }
        that._renderItemData( ul, item );
      });
    }
  });
  
  
