function ap_send_to_editor( text ) {
	
	var win = window.dialogArguments || opener || parent || top;
	win.send_to_editor( text );
	
}