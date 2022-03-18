2 Task

Since this is a code review, I will just put comments and will not fix this line
1. About path:
     Please, use the constant for domain endpoint (https://api.supermetrics.com/)
     Please, use URL encoding function, according to https://www.rfc-editor.org/rfc/rfc3986 (see 2.2) these characters should be encoded (@ = %40)
2. Method:
     The allowed method for this endpoint is POST, despite the fact, that we use GET at the moment,
         sure we can use file_get_contents with post method, by adding their third parameter with
         required method context (file_get_contents('...', false, ['http' => ['method' => 'POST']])), but this is not so obviously for
         other developers, could you please use standard CURL methods or any HTTP library for this?
                                                                                            3. Params:
     So, according to the documentation, params in most cases means query parameters, but this is not obviously understandable
         why do we use the POST method with the empty body?
         I will assume that params mean body, in this case, we also need to add a header.
         Since we don't know whether the API is REST-full or not, we will not add Content-Type: application/json
         (but obviously, we should clarify this information, and in most cases, REST-full API should return JSON with a status),
         we will assume, that API doesn't require JSON, therefore we will encode all params with a URL encoding method
         for this request. Please add the header 'Content-Type: application/x-www-form-urlencoded' for this request
         if we use file_get_contents, then file_get_contents('...', false, ['http' => ['header' => ['Content-Type' => 'application/x-www-form-urlencoded']]])
4. Response:
     According to documentation, we don't know what is the response, JSON or plain text, or XML. If the response is JSON,
         obviously we will use json_decode($response, true), and receive token via its key ['sl_token']. Other information is received
         for informational purposes only, therefore, let's use only sl_token and other information should be just checked or logged


3 Task is completed in the file 3rd_task.php

4 Task:

If the library or the class totally fulfills the purposes of the current tasks, then I don't see the reason why we should not add the library/class into our project after total consideration. If after seeing the code we didn't find any security issues, we will add that library in our project via creating one abstract layer between our Domain layer or Application layer (depending on the current tasks and the tasks that the library fulfills) that will be added as a dependency for current layer and the library will realize interface of that abstraction (will be used via Dependency Injection). Also, if it is necessary, we can add Adapters in order to comply with the interface between the library and our application


Sorry, but the first task is taking too long, at the moment current work we are in the deployment phase of a very important feature and as a key developer, I don't have enough time to deploy testing assignments, learn architecture, and write production testable code for the testing task.
For a first test assignment, however, I can complete this task after a technical interview, or if you want to see the code
what can I write, I opened the repository with my home project
https://github.com/urlimited/househub/tree/master/househub_monolith_mvp/app
The most actual version is on the T6 branch. You can also find some feature tests there.