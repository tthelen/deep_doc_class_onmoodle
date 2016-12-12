pred = read.csv("/tmp/tmp/results20282/predictions/prediction_batch0.csv")

files = read.csv("/tmp/tmp/course_20282.csv")

comb = merge(pred,files,by.x = "id", by.y = "document_id")
