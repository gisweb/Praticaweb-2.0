function validateMonth(field, bypassUpdate) {
	var input = field.value
	if (isEmpty(input)) {
		alert(“Be sure to enter a month value.”)
		select(field)
		return false
	} 
	else {
		input = parseInt(field.value, 10)
		if (isNaN(input)) {
			alert(“Entries must be numbers only.”)
			select(field)
			return false
		} 
		else {
			if (!inRange(input,1,12)) {
				alert(“Enter a number between 1 (January) and 12 (December).”)
				select(field)
				return false
			}
		}
	}
	if (!bypassUpdate) {
		calcDate()
	}
	return true
}

function validateDate(field) {
	var input = field.value
	if (isEmpty(input)) {
	alert(“Be sure to enter a date value.”)
	select(field)
	return false
	} else {
	input = parseInt(field.value, 10)
	if (isNaN(input)) {
	alert(“Entries must be numbers only.”)
	select(field)
	return false
	} else {
	var monthField = document.birthdate.month
	if (!validateMonth(monthField, true)) return false
	var monthVal = parseInt(monthField.value, 10)
	var monthMax = new Array(31,31,29,31,30,31,30,31,31,30,31,30,31)
	var top = monthMax[monthVal]
	if (!inRange(input,1,top)) {
	alert(“Enter a number between 1 and “ + top + “.”)
	select(field)
	return false
	}
	}
	}
	calcDate()
	return true
	}
	
function validateYear(field) {
	var input = field.value
	if (isEmpty(input)) {
		alert(“Be sure to enter a year value.”)
		select(field)
		return false
	} 
	else {
		input = parseInt(field.value, 10)
		if (isNaN(input)) {
			alert(“Entries must be numbers only.”)
			select(field)
			return false
		} 
		else {
			if (!inRange(input,1900,2025)) {
				alert(“Enter a number between 1900 and 2025.”)
				select(field)
				return false
			}
		}
	}
	calcDate()
	return true
}

function select(field) {
	field.focus()
	field.select()
}

function calcDate() {
	var mm = parseInt(document.birthdate.month.value, 10)
	var dd = parseInt(document.birthdate.date.value, 10)
	var yy = parseInt(document.birthdate.year.value, 10)
	document.birthdate.fullDate.value = mm + “/” + dd + “/” + yy
}