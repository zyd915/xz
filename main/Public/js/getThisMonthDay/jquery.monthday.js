;(function($){
			$.fn.calculateDays = function (options){
				var defaults = {
					needMonthName : 'thismonth',
					needDaysName :'thisdays',
					needYearName: 'thisyear',
					thisUrl : 'thisUrl',
				};
			var option = $.extend(defaults,options);
			var needYearName = option.needYearName;
			var needMonthName = option.needMonthName;
			var needDaysName = option.needDaysName;
			var thisUrl = option.thisUrl;
			$(this).change(function(){
				var year = $('select[name='+needYearName+']').find("option:selected").text();
				var month = $('select[name='+needMonthName+']').find("option:selected").text();
				$.post(thisUrl,{'year':year,'month':month},function(ev){
				$('select[name='+needDaysName+']').html('');
				var thisdays = '';
				for(var i=1;i<=ev;i++) {
					$('select[name='+needDaysName+']').append("<option value="+i+">"+i+"</option");
				}			
				},'json');
			});
		}	
		
		})(jQuery);
		