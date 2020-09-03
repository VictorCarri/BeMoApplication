$(document).ready(function(e)
	{
		$("#setEmail").click(function(e)
			{
				e.preventDefault();
				$.post("./admHandler.php",
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

		$("input:checkbox").click(function(e)
			{
				//e.preventDefault();
				const isChecked = $(this).prop("checked");
				console.log(isChecked);
				//alert(isChecked);
				pageURL = $(this).attr("id");
				$.post("./admHandler.php",
					{
						indexable: isChecked,
						pageURL: pageURL
					},
					function(resp)
					{
						resp = JSON.parse(resp);
						console.log(resp);
						//alert(resp);
						
						if (resp.hasOwnProperty("failure")) // The operation failed
						{
							alert("Couldn't change page " + pageURL + "'s indexable status: " + resp["failure"]);
						}
	
						else if (resp.hasOwnProperty("success")) // The operation succeeded
						{
							alert("Successfuly changed page " + pageURL + "'s indexable status.");
							//$("#" + pageURL).prop("checked", !isChecked);
							//this.checked = !this.checked;
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
				$.post("./admHandler.php",
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
					"./admHandler.php",
					{
						service: service,
						code: $("textarea[data-for=\"" + $(this).attr("id") + "\"]").val()
					},
					function(resp)
					{
						console.log(resp);
						resp = JSON.parse(resp);

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

		$(".imgButton").click(function(e)
			{
				e.preventDefault();
				const purpose = $(this).attr("data-purpose");
				console.log($("#" + purpose));

				if ($("#" + purpose)[0].files.length === 0) // No file
				{
					alert("You haven't uploaded any files.");
				}

				else
				{
					alert("Uploading file...");
					const fileData = $("#" + purpose).prop("files")[0];
					console.log(fileData);
					var formData = new FormData();
					formData.append("file", fileData);
					formData.append("purpose", purpose);
					formData.append("replaceImg", true);
					$.ajax("./admHandler.php", {
							type: "POST",
							data: formData,
							cache: false,
							contentType: false,
							processData: false,
							success: function(data, status, xhr)
							{
								const parsedData = JSON.parse(data);
								console.log("%o, %o,  %o", parsedData, status, xhr);
								
								if (parsedData.hasOwnProperty("success"))
								{
									alert("Successfully changed image");
									$("img[data-purpose=\"" + purpose + "\"]").attr("src", parsedData.success);
								}

								else
								{
									alert("Failed to change image: " + parsedData.failure);
								}
							}
						}
					);
				}
			}
		);
	}
);
