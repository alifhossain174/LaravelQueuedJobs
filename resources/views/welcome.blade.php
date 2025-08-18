<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create New Task</title>
    <link rel="stylesheet" href="{{url('/')}}/css/toastr.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .task-form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            overflow: hidden;
        }

        .form-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #d1d5db;
        }

        .form-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
        }

        .form-content {
            padding: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            color: #1f2937;
            transition: border-color 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            color: #1f2937;
            resize: vertical;
            min-height: 80px;
            font-family: inherit;
            transition: border-color 0.2s ease;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #3b82f6;
        }

        .checkbox-item label {
            margin: 0;
            font-size: 14px;
            color: #374151;
            cursor: pointer;
        }

        .create-button {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-top: 24px;
        }

        .create-button:hover {
            background-color: #2563eb;
        }

        .create-button:active {
            background-color: #1d4ed8;
            transform: translateY(1px);
        }
    </style>
</head>
<body>
    <div class="task-form-container">
        <div class="form-header">
            <h1>Create New Task</h1>
        </div>

        <div class="form-content">
            <form id="taskForm">

                <div class="form-group">
                    <label for="taskName">Task Name</label>
                    <input type="text" id="taskName" class="form-input" placeholder="Enter task name">
                </div>

                <div class="form-group">
                    <label for="taskDescription">Task Description</label>
                    <textarea id="taskDescription" class="form-textarea" placeholder="Enter task description"></textarea>
                </div>

                <div class="form-group">
                    <label>Assign Users</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" id="user1" value="alifhossain174@gmail.com">
                            <label for="user1">Alif Hossain (alifhossain174@gmail.com)</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="user2" value="fahimmit05@gmail.com">
                            <label for="user2">Fahim Hossain (fahimmit05@gmail.com)</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="user3" value="bawinofficial@gmail.com">
                            <label for="user3">Mr. Bawin (bawinofficial@gmail.com)</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="user4" value="bawinofficial@gmail.com">
                            <label for="user4">Mrs. Jebs (martinahossain4256@gmail.com)</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="create-button">Create Task</button>
            </form>

            <a href="{{url('exports/tasks/queue')}}" style="display: inline-block; margin-top: 50px;">Export Task Data</a>
        </div>
    </div>

    <script>
        document.getElementById('taskForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const taskName = document.getElementById('taskName').value;
            const taskDescription = document.getElementById('taskDescription').value;
            const assignedUsers = [];

            // Get selected users with their values instead of full labels
            document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
                assignedUsers.push(checkbox.value);
            });

            if (!taskName.trim()) {
                alert('Please enter a task name');
                return;
            }

            // Disable the submit button to prevent multiple submissions
            const submitButton = document.querySelector('.create-button');
            submitButton.disabled = true;
            submitButton.textContent = 'Creating...';

            // AJAX POST request to Laravel
            fetch('/send/task', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: taskName,
                    description: taskDescription,
                    assigned_users: assignedUsers
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                //alert('Task created successfully!');
                //console.log('Success:', data);
                toastr.success('Task Assigned successfully!');
                // Reset form on success
                this.reset();
            })
            .catch(error => {
                // console.error('Error:', error);
                // alert('Error creating task. Please try again.');
                toastr.error('Error creating task. Please try again.');
            })
            .finally(() => {
                // Re-enable the submit button
                submitButton.disabled = false;
                submitButton.textContent = 'Create Task';
            });
        });
    </script>

    <script src="{{url('/')}}/js/jquery.min.js"></script>
    <script src="{{url('/')}}/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

</body>
</html>
