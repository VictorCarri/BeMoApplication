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
	}
);
