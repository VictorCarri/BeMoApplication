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
				e.preventDefault();
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

		$("button.pageInfo").click(function(e)
			{
				e.preventDefault();
				const url = $(this).attr("data-url");
				const type = $(this).attr("data-for");
				const newContent = $("input[data-url=\"" + url + "\"]").val();
				const msg = "Click on button for " + type + " of page " + url + ", with new content \"" + newContent + "\"";
				console.log(msg);
				//alert(msg);
				$.post("./index.php",
					{
						url: url,
						type: type,
						content: newContent
					},
					function(resp)
					{
						console.log(resp);
					
						if (resp.hasOwnProperty("failure")) // Failure
						{
							alert(resp.failure);
						}

						else
						{
							alert("Successfully updated page info");
						}
					}
				);
			}
		);

		$(".updateAnalytics").click(function(e)
			{
				const services = {
					ga: "Google Analytics",
					fb: "Facebook Pixel"
				};
				const service = services[$(this).attr("id")];
				console.log("Selected service: %s", service);
				$.post(
					"./index.php",
					{
						service: service,
						code: $("textarea[data-for=\"" + $(this).attr("id") + "\"]").val()
					},
					function(resp)
					{
						console.log(resp);

						if (resp.hasOwnProperty("failure")) // Failure
						{
							alert(resp.failure);
						}

						else
						{
							alert("Successfully updated analytics info");
						}
					}
				);
			}
		);
	}
);
