$(document).ready(function(e)
	{
		$("#setEmail").click(function(e)
			{
				e.preventDefault();
				$.post("./index.php",
					{
						email: $("#admEmInp").val()
					},

					function(resp)
					{
						resp = JSON.parse(resp);
						console.log(resp);

						if (resp.setEmRes == "invalidEmail")
						{
							alert("The email you entered is invalid.");
						}

						else
						{
							alert("Successfully set email");
						}
					}
				);
			}
		);
		$("input[type=checkbox]").click(function(e)
			{
				console.log(this.checked);
				//alert(this.checked);
				pageURL = $(this).attr("id");
				$.post("./index.php",
					{
						indexable: this.checked,
						pageURL: pageURL
					},
					function(resp)
					{
						console.log(resp);
						//alert(resp);
						
						if (resp.hasOwnProperty("failure")) // The operation failed
						{
							alert("Couldn't change page " + pageURL + "'s indexable status: " + resp["failure"]);
						}
	
						else if (resp.hasOwnProperty("success")) // The operation succeeded
						{
							alert("Successfuly changed page " + pageURL + "'s indexable status.");
						}
					}
				);
			}
		);
	}
);
