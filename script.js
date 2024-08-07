
function toggleClassSelection(value) {
    const classGroup = document.getElementById('class_group');
    if (value === 'Yes') {
        classGroup.classList.remove('hidden');
    } else {
        classGroup.classList.add('hidden');
        document.getElementById('class').value = ''; // Clear the class selection
    }
}

// Function to toggle attendance status and update UI
function toggleAttendance(studentId, status) {
    var presentBtn = document.getElementById('present-' + studentId);
    var absentBtn = document.getElementById('absent-' + studentId);

    if (status === 'present') {
        presentBtn.classList.add('present-active');
        absentBtn.classList.remove('absent-active');
        document.getElementById('status-' + studentId).value = 'Present';
    } else {
        absentBtn.classList.add('absent-active');
        presentBtn.classList.remove('present-active');
        document.getElementById('status-' + studentId).value = 'Absent';
    }
}


// JavaScript for Reset Button
document.addEventListener('DOMContentLoaded', function() {
    // Select the reset button
    const resetBtn = document.getElementById('resetBtn');

    // Add click event listener
    resetBtn.addEventListener('click', function() {
        // Reset the form inputs and any displayed search results
        document.getElementById('searchInput').value = ''; // Replace 'searchInput' with your actual input ID
        document.getElementById('searchResults').innerHTML = ''; // Replace 'searchResults' with your actual results container ID

        // Optionally, you can hide the reset button after resetting
        resetBtn.style.display = 'none';
    });
});


// Function to submit attendance using AJAX
function submitAttendance() {
    var form = document.getElementById('attendanceForm');
    var formData = new FormData(form);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'tc_attd_submit.php', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Handle success or show confirmation
            alert('Attendance submitted successfully');
        } else {
            // Handle error
            alert('Error submitting attendance');
        }
    };
    xhr.send(formData);
}

function togglePassword(element) {
    const input = element.previousElementSibling;
    if (input.type === "password") {
        input.type = "text";
        element.classList.remove('bx-show');
        element.classList.add('bx-hide');
    } else {
        input.type = "password";
        element.classList.remove('bx-hide');
        element.classList.add('bx-show');
    }
}

const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

allSideMenu.forEach(item=> {
	const li = item.parentElement;

	item.addEventListener('click', function () {
		allSideMenu.forEach(i=> {
			i.parentElement.classList.remove('active');
		})
		li.classList.add('active');
	})
});

const Confirm = {
    open(options) {
        // Your existing Confirm object implementation
        // Ensure this is defined only once in this file
        // Options for confirmation dialog
        options = Object.assign({}, {
            title: '',
            message: '',
            okText: 'OK',
            cancelText: 'Cancel',
            onok: function() {},
            oncancel: function() {}
        }, options);

        // Create HTML template for confirmation dialog
        const html = `
            <div class="confirm">
                <!-- Your confirmation dialog HTML structure -->
            </div>
        `;

        // Append HTML template to document body
        // Handle events for OK, Cancel, and close button
        document.body.appendChild(template.content);
    },

    _close(confirmEl) {
        // Function to close the confirmation dialog
        // Remove confirmation dialog from document body
    }
};


document.addEventListener('DOMContentLoaded', function() {
	// Your script code here, including element selection and event listener
  });

  const markPresentButtons = document.querySelectorAll('.mark-present');

  markPresentButtons.forEach(button => {
	button.addEventListener('click', function() {
	  // Simulate changing status (replace with actual logic)
	  const statusElement = this.parentElement.querySelector('.status');
	  statusElement.classList.remove('absent');
	  statusElement.classList.add('present');
	  statusElement.textContent = 'Present';
  
	  // Change button text or disable it (optional)
	  // this.textContent = 'Recorded';
	  // this.disabled = true;
	});
  });
  
const addStudentBtn = document.getElementById('add-student-btn');
const studentFormContainer = document.querySelector('.student-form');
const removeLastStudentBtn = document.getElementById('remove-last-student-btn');

addStudentBtn.addEventListener('click', () => {
  const newStudentForm = studentFormContainer.cloneNode(true);
  studentFormContainer.parentNode.appendChild(newStudentForm);
});

// Function to remove the last added student form
removeLastStudentBtn.addEventListener('click', () => {
    const forms = document.querySelectorAll('.student-form');
    if (forms.length > 1) { // Ensure there is at least one student form left
        const lastForm = forms[forms.length - 1];
        lastForm.parentNode.removeChild(lastForm);
    } else {
        alert("Cannot remove the last student form.");
    }
});

const isClassTeacherRadios = document.querySelectorAll('input[name="is_class_teacher"]');
        const classAssignmentDiv = document.getElementById('class_assignment');

        isClassTeacherRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (document.getElementById('is_class_teacher_yes').checked) {
                    classAssignmentDiv.style.display = 'block';
                } else {
                    classAssignmentDiv.style.display = 'none';
                }
            });
        });

//Filter Function
function showFilterOptions() {
    var filterOptions = document.getElementById('filterOptions');
    filterOptions.style.display = filterOptions.style.display === 'none' ? 'block' : 'none';
}

function applyFilter() {
    var noticeType = document.getElementById('noticeTypeFilter').value;
    var time = document.getElementById('timeFilter').value;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'filter_notices.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (this.status == 200) {
            document.getElementById('notices').innerHTML = this.responseText;
        }
    };
    
    var params = 'notice_type=' + encodeURIComponent(noticeType) + '&time=' + encodeURIComponent(time);
    xhr.send(params);
}

        
// script.js


  //Filter
function toggleFilter() {
	var filterDropdown = document.getElementById("filterDropdown");
	if (filterDropdown.style.display === "block") {
		filterDropdown.style.display = "none";
	} else {
		filterDropdown.style.display = "block";
	}
}

// TOGGLE SIDEBAR
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');
})

//Login
document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();
    // Validate the login form
    if (validateLoginForm()) {
        // Redirect the user to the dashboard page
        window.location.href = 'admin_index.html';
    }
});

function validateLoginForm() {
    // Validate the login form
    // Return true if the form is valid, false otherwise
}




const searchButton = document.querySelector('#content nav form .form-input button');
const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
const searchForm = document.querySelector('#content nav form');

searchButton.addEventListener('click', function (e) {
	if(window.innerWidth < 576) {
		e.preventDefault();
		searchForm.classList.toggle('show');
		if(searchForm.classList.contains('show')) {
			searchButtonIcon.classList.replace('bx-search', 'bx-x');
		} else {
			searchButtonIcon.classList.replace('bx-x', 'bx-search');
		}
	}
})

document.getElementById("viewStudentProfile").addEventListener("click", function() {
    document.getElementById("studentProfileSection").style.display = "block";
    document.querySelector(".profile-section").style.display = "none";
});




if(window.innerWidth < 768) {
	sidebar.classList.add('hide');
} else if(window.innerWidth > 576) {
	searchButtonIcon.classList.replace('bx-x', 'bx-search');
	searchForm.classList.remove('show');
}


window.addEventListener('resize', function () {
	if(this.innerWidth > 576) {
		searchButtonIcon.classList.replace('bx-x', 'bx-search');
		searchForm.classList.remove('show');
	}
})

document.addEventListener("DOMContentLoaded", function() {
    const profileDropdown = document.getElementById("profileDropdown");
    const dropdownMenu = document.getElementById("dropdownMenu");

    // Show dropdown menu on hover
    profileDropdown.addEventListener("mouseover", function() {
        dropdownMenu.style.display = "block";
    });

    // Hide dropdown menu when mouse leaves
    profileDropdown.addEventListener("mouseout", function() {
        dropdownMenu.style.display = "none";
    });
});





const switchMode = document.getElementById('switch-mode');

switchMode.addEventListener('change', function () {
	if(this.checked) {
		document.body.classList.add('dark');
	} else {
		document.body.classList.remove('dark');
	}
})



// Encapsulate the script to avoid polluting the global scope
(function() {
    
  // Logout Confirmation Popup
  document.addEventListener('DOMContentLoaded', function() {
      // Add event listener for logout button
      const logoutButton = document.querySelector('.logout');
      if (logoutButton) {
          logoutButton.addEventListener('click', function(event) {
              event.preventDefault(); // Prevent the default form submission

              Confirm.open({
                  title: 'Confirm Logout',
                  message: 'Are you sure you want to logout?',
                  okText: 'Logout',
                  onok: () => {
                      // Perform logout action
                      location.href = 'logout_users.php'; // Adjust the logout URL as per your project
                  }
              });
          });
      }

      // Add event listener for delete buttons
      document.querySelectorAll('.delete-btn').forEach(button => {
          button.addEventListener('click', function() {
              const noticeId = this.getAttribute('data-id');
              Confirm.open({
                  title: 'Confirm Notice Deletion',
                  message: 'Are you sure you want to delete this notice?',
                  onok: () => {
                      location.href = 'staff_delete_notice.php?id=' + noticeId;
                  }
              });
          });
      });
  });

  // Confirmation Popup Logic
  const Confirm = {
      open(options) {
          options = Object.assign({}, {
              title: '',
              message: '',
              okText: 'Delete',
              cancelText: 'Cancel',
              onok: function() {},
              oncancel: function() {}
          }, options);

          const html = `
              <div class="confirm">
                  <div class="confirm__window">
                      <div class="confirm__titlebar">
                          <span class="confirm__title">${options.title}</span>
                          <button class="confirm__close">&times;</button>
                      </div>
                      <div class="confirm__content">${options.message}</div>
                      <div class="confirm__buttons">
                          <button class="confirm__button confirm__button--ok confirm__button--fill">${options.okText}</button>
                          <button class="confirm__button confirm__button--cancel">${options.cancelText}</button>
                      </div>
                  </div>
              </div>
          `;

          const template = document.createElement('template');
          template.innerHTML = html;

          // Elements
          const confirmEl = template.content.querySelector('.confirm');
          const btnClose = template.content.querySelector('.confirm__close');
          const btnOk = template.content.querySelector('.confirm__button--ok');
          const btnCancel = template.content.querySelector('.confirm__button--cancel');

          confirmEl.addEventListener('click', e => {
              if (e.target === confirmEl) {
                  options.oncancel();
                  this._close(confirmEl);
              }
          });

          btnOk.addEventListener('click', () => {
              options.onok();
              this._close(confirmEl);
          });

          [btnCancel, btnClose].forEach(el => {
              el.addEventListener('click', () => {
                  options.oncancel();
                  this._close(confirmEl);
              });
          });

          document.body.appendChild(template.content);
      },

      _close(confirmEl) {
          confirmEl.classList.add('confirm--close');

          confirmEl.addEventListener('animationend', () => {
              document.body.removeChild(confirmEl);
          });
      }
  };
})();

