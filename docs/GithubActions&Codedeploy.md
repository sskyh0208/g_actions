# GithubActions & Codedeploy

## はじめに

このドキュメントでは、GitHub Actionsを使用してアプリケーションの自動ビルドを設定し、AWS CodeDeployを通じてEC2インスタンスへのデプロイを行うプロセスについて説明します。

## 目的

このプロセスを通じて、開発の自動化、品質の向上、リリースの高速化を目指します。

## 前提条件

- GitHubリポジトリが設定されていること。
- AWSアカウントがあり、CodeDeployとEC2にアクセスできること。
- 必要なAWS IAMロールとポリシーが設定されていること。

## アーキテクチャ

簡単なシステムアーキテクチャ図とともに、CI/CDパイプラインの全体像を説明します。

## GitHub Actionsの設定

1. ワークフローのトリガー:  
    - 検証環境　→ プルリクエストイベント
    - 本番環境　→ マニュアルトリガー（workflow_dispatch）

2. ビルド環境の設定:  
    - ubuntu-latest
    - Repository secrets

### Repository Secrets

| Name | Value |
|:---|:---|
|PROD_AWS_DEPLOY_APP_NAME|本番環境用デプロイアプリケーション名|
|PROD_AWS_DEPLOY_GROUP_NAME|本番環境用デプロイグループ名|
|PROD_AWS_DEPLOY_SOURCE_BUCKET_NAME|本番環境用デプロイソースバケット名|
|PROD_AWS_PARAMETER_STORE_ENV_FILE_NAME|本番環境用.envファイルパラメータストア名|
|STG_AWS_DEPLOY_APP_NAME|本番環境用デプロイアプリケーション名|
|STG_AWS_DEPLOY_GROUP_NAME|本番環境用デプロイグループ名|
|STG_AWS_DEPLOY_SOURCE_BUCKET_NAME|本番環境用デプロイソースバケット名|
|STG_AWS_PARAMETER_STORE_ENV_FILE_NAME|本番環境用.envファイルパラメータストア名|
|AWS_ROLE_TO_ASSUME|デプロイ用IAMロール　ARN|
|SLACK_WEBHOOK_URL|デプロイ通知用SlackチャンネルWebHookURL|


環境変数の設定
依存関係のインストール:

必要なライブラリやツールのインストール
ビルドプロセス:

ビルドコマンドの実行
テストスクリプトの実行
ビルド成果物の準備:

成果物の圧縮
S3へのアップロード