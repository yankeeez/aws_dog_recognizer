State machine code:
```
{
  "Comment": "A Hello World example of the Amazon States Language using Pass states",
  "StartAt": "RecognizeImage",
  "States": {
    "RecognizeImage": {
      "Type": "Task",
      "Resource": "arn:aws:lambda:us-east-2:915263672290:function:app-dev-imageRekognitionFunction",
      "TimeoutSeconds": 3,
      "ResultPath": "$.results.labels",
      "Next": "checkIsDogInPicture",
      "Catch": [
        {
          "ErrorEquals": ["States.All"],
          "Next": "QuitMain"
        }
      ]
    },
   "checkIsDogInPicture": {
      "Type": "Task",
      "Resource": "arn:aws:lambda:us-east-2:915263672290:function:app-dev-checkDogInPicture",
      "TimeoutSeconds": 3,
      "ResultPath": "$.isDog",
      "Next": "ProcessResult",
      "Catch": [
        {
          "ErrorEquals": ["States.All"],
          "Next": "QuitMain"
        }
      ]
    },
    "ProcessResult": {
      "Type": "Choice",
      "Choices": [
        {
          "Variable": "$.isDog",
          "BooleanEquals": true,
          "Next": "SaveInfoToDB"
        },
        {
          "Variable": "$.isDog",
          "BooleanEquals": false,
          "Next": "SendEmail"
        }
      ]
    },
    "SaveInfoToDB": {
      "Type": "Task",
      "Resource": "arn:aws:lambda:us-east-2:915263672290:function:app-dev-saveImageInfo",
      "TimeoutSeconds": 3,
      "ResultPath": "$.results.path",
      "OutputPath": "$.results",
      "End": true,
      "Catch": [
        {
          "ErrorEquals": ["States.All"],
          "Next": "QuitMain"
        }
      ]
    },
    "SendEmail": {
      "Type": "Task",
      "Resource": "arn:aws:lambda:us-east-2:915263672290:function:app-dev-sendEmail",
      "TimeoutSeconds": 3,
      "ResultPath": "$.results.path",
      "End": true,
      "Catch": [
        {
          "ErrorEquals": ["States.All"],
          "Next": "QuitMain"
        }
      ]
    },
    "QuitMain": {
      "Type": "Fail",
      "Error": "GenericError",
      "Cause": "An error occured while executing state machine!"
    }
  }
}
```
